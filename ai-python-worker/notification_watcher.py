"""
FCM Notification Watcher using MongoDB Change Streams.
Listens for status updates to 'completed' or 'failed' and sends notifications.
"""
import sys
import os
from datetime import datetime
from core.db import db
from core.notifier import notify_processing_complete

# Watch the 'processing_requests' collection as requested
# (Note: Earlier analysis showed Laravel might be writing to 'services', 
# but the task specifically requests 'processing_requests')
collection = db["services"]


def watch_notifications():
    """
    Step 2: Listen to collection using Change Stream.
    Step 7: Infinite Listener.
    """
    pipeline = [
        {
            "$match": {
                "operationType": "update",
                # Step 2: status changed to "completed" OR "failed"
                "updateDescription.updatedFields.status": {"$in": ["completed", "failed"]},
                # Step 3: Prevent Duplicate Notifications
                "fullDocument.notification_sent": {"$ne": True}
            }
        }
    ]

    print(f"[WATCHER] Listening for job status changes in '{collection.name}' collection...")

    try:
        # Step 7: Infinite Listener with fullDocument lookup
        with collection.watch(pipeline, full_document="updateLookup") as stream:
            for change in stream:
                # Step 8: Logging - Job detected
                doc = change.get("fullDocument")
                if not doc:
                    continue

                job_id = doc.get("_id")
                status = doc.get("status")
                user_id = doc.get("user_id")
                service_type = doc.get("type", "unknown")
                file_id = doc.get("file_id")

                print(f"\n[DETECTED] Job {job_id} changed status to '{status}'")

                # Step 8: Logging - Missing user_id
                if not user_id:
                    print(f"[SKIP] No user_id found for job {job_id}. Skipping notification.")
                    continue

                # Step 4: Send Notification
                success = (status == "completed")
                
                # Step 5: Error Safety
                try:
                    notification_result = notify_processing_complete(
                        user_id=user_id,
                        service_type=service_type,
                        file_id=file_id,
                        success=success
                    )

                    if notification_result:
                        # Step 6: Mark as Notified
                        collection.update_one(
                            {"_id": job_id},
                            {
                                "$set": {
                                    "notification_sent": True,
                                    "notified_at": datetime.utcnow()
                                }
                            }
                        )
                        print(f"[SUCCESS] Notification sent and job {job_id} marked as notified.")
                    else:
                        # Step 8: Logging - Notification failed
                        print(f"[FAILED] notify_processing_complete returned False for job {job_id}.")

                except Exception as notify_err:
                    # Step 5: If notification fails -> log error, DO NOT crash watcher
                    print(f"[ERROR] Exception during notification process for job {job_id}: {str(notify_err)}")

    except Exception as stream_err:
        print(f"[FATAL] Change stream error: {str(stream_err)}")
        sys.exit(1)

if __name__ == "__main__":
    print("FCM Notification Watcher Started")
    watch_notifications()
