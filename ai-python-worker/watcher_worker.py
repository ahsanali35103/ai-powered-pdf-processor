from pymongo import ReturnDocument
import threading
from datetime import datetime

# ---- DB ----
from core.db import requests_collection

# ---- WORKERS (FIXED PATHS) ----
from processors.ocr_worker import OCRWorker
from processors.summarizer.summarize_worker import run_service as summarize_service
from processors.Translator.translate_worker import run_service as translate_service

from core.notifier import notify_processing_complete

collection = requests_collection


# =========================
# JOB HANDLER
# =========================
def run_job(doc):

    job_id = doc["_id"]
    service_type = doc.get("type")

    print(f"\n🚀 run_job started -> {job_id} | {service_type}")

    try:

        # ================= OCR =================
        if service_type == "ocr":
            print("🧠 OCR Worker Triggered")
            worker = OCRWorker()
            worker.run_ocr(doc)

        # ================= SUMMARIZE =================
        elif service_type == "summarization":
            print("🧠 Summarizer Worker Triggered")
            summarize_service(doc)

        # ================= TRANSLATE =================
        elif service_type == "translation":
            print("🧠 Translator Worker Triggered")
            translate_service(doc)

        else:
            raise Exception(f"Unknown service type: {service_type}")

        print(f"✅ Job Completed -> {job_id}")

        notify_processing_complete(
            user_id=doc.get("user_id"),
            service_type=service_type,
            file_id=doc.get("file_id"),
            success=True
        )

    except Exception as e:

        print(f"❌ Job Failed -> {job_id}: {str(e)}")

        collection.update_one(
            {"_id": job_id},
            {"$set": {
                "status": "failed",
                "error": str(e),
                "completed_at": datetime.utcnow()
            }}
        )

        notify_processing_complete(
            user_id=doc.get("user_id"),
            service_type=service_type,
            file_id=doc.get("file_id"),
            success=False
        )


# =========================
# CHANGE STREAM WATCHER
# =========================
def watch():

    print("👀 Mongo Change Stream Started...")

    with collection.watch(full_document="updateLookup") as stream:

        print("✅ Watching collection... waiting for jobs")

        for change in stream:

            try:
                print("\n🔥 CHANGE RECEIVED")

                if change["operationType"] != "insert":
                    continue

                doc = change["fullDocument"]
                print("📄 DOC:", doc)

                if doc.get("status") != "pending":
                    print("⏭️ Not pending, skipping")
                    continue

                job_id = doc["_id"]

                # LOCK JOB
                locked = collection.find_one_and_update(
                    {"_id": job_id, "status": "pending"},
                    {"$set": {
                        "status": "processing",
                        "started_at": datetime.utcnow()
                    }},
                    return_document=ReturnDocument.AFTER
                )

                print("🔒 LOCK:", locked)

                if locked:
                    t = threading.Thread(target=run_job, args=(locked,))
                    t.daemon = True
                    t.start()
                else:
                    print("⏭️ Already processed")

            except Exception as e:
                print("❌ Stream error:", str(e))


if __name__ == "__main__":
    watch()