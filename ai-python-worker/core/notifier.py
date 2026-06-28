import firebase_admin
from firebase_admin import credentials, messaging
import os
from core.db import users_collection
from bson import ObjectId

# Initialize Firebase Admin SDK
# Try to find the service account file in a few common locations
possible_paths = [
    os.getenv("FIREBASE_CREDS_PATH"),
    r"c:\Users\Hp\AI-Powered-pdf-Processer\ai-pdf-laravel\storage\app\firebase\chat-5810e.json",
    os.path.join(os.path.dirname(__file__), "..", "..", "ai-pdf-laravel", "storage", "app", "firebase", "chat-5810e.json"),
    "/var/www/ai-pdf-laravel/storage/app/firebase/chat-5810e.json" # Common Linux path
]

firebase_creds_path = None
for path in possible_paths:
    if path and os.path.exists(path):
        firebase_creds_path = path
        break

if not firebase_creds_path:
    print("[NOTIFIER] CRITICAL: Firebase Service Account JSON not found. Notifications will fail.")

if firebase_creds_path and not firebase_admin._apps:
    cred = credentials.Certificate(firebase_creds_path)
    firebase_admin.initialize_app(cred)


def send_notification(user_id, title, body, data=None):
    """
    Send FCM notification to a specific user
    """
    try:
        # 1. Get user's FCM token from database
        # Support both UUID strings and ObjectIds
        query = {"$or": [
            {"_id": str(user_id)},
            {"id": str(user_id)}
        ]}
        
        # Also try as ObjectId if it looks like one
        try:
            if isinstance(user_id, str) and len(user_id) == 24:
                query["$or"].append({"_id": ObjectId(user_id)})
            elif isinstance(user_id, ObjectId):
                query["$or"].append({"_id": user_id})
        except:
            pass

        user = users_collection.find_one(query)
        
        if not user:
            print(f"[NOTIFIER] User {user_id} not found")
            return False
            
        fcm_token = user.get("fcm_token")
        if not fcm_token:
            print(f"[NOTIFIER] User {user_id} has no FCM token stored")
            return False

        # 2. Construct message
        message = messaging.Message(
            notification=messaging.Notification(
                title=title,
                body=body,
            ),
            data=data or {},
            token=fcm_token,
        )

        # 3. Send message
        response = messaging.send(message)
        print(f"[NOTIFIER] Successfully sent message to user {user_id}: {response}")
        return True

    except Exception as e:
        print(f"[NOTIFIER] Error sending notification: {str(e)}")
        return False

def notify_processing_complete(user_id, service_type, file_id, success=True):
    """
    Helper to send standard processing completion notification
    """
    status = "successfully" if success else "with errors"
    title = f"Processing {service_type.upper()} Complete"
    body = f"Your file processing for {service_type} has finished {status}."
    
    data = {
        "file_id": str(file_id),
        "service_type": service_type,
        "status": "completed" if success else "failed"
    }
    
    return send_notification(user_id, title, body, data)
