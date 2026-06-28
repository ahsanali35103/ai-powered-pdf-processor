from core.db import requests_collection
from core.ocr_service import OCRService
from processors.summarizer.summarize import summarize_text
from core.response import success_response, error_response
from datetime import datetime


def run_service(service):

    print("📄 Summarization Worker Started...")

    service_id = service["_id"]

    try:
        # =========================
        # UPDATE STATUS → PROCESSING
        # =========================
        requests_collection.update_one(
            {"_id": service_id},
            {"$set": {"status": "processing"}}
        )

        # =========================
        # GET FILE ID
        # =========================
        file_id = service.get("file_id")

        if not file_id:
            raise Exception("file_id missing")

        # =========================
        # OCR STEP (FIXED)
        # =========================
        text = OCRService.process_to_text(file_id)

        if not text or not text.strip():
            raise Exception("OCR returned empty text")

        # =========================
        # SUMMARIZATION STEP
        # =========================
        summarized_text = summarize_text(text)

        if not summarized_text:
            raise Exception("Summarization failed")

        # =========================
        # SUCCESS RESPONSE
        # =========================
        output = success_response({
            "summarized_text": summarized_text,
            "original_length": len(text),
            "summary_length": len(summarized_text)
        })

        requests_collection.update_one(
            {"_id": service_id},
            {"$set": {
                "status": "completed",
                "output": output,
                "completed_at": datetime.utcnow()
            }}
        )

        print(f"✅ Summarization Completed: {service_id}")

    except Exception as e:

        # =========================
        # FAILURE HANDLING
        # =========================
        requests_collection.update_one(
            {"_id": service_id},
            {"$set": {
                "status": "failed",
                "output": error_response(str(e)),
                "completed_at": datetime.utcnow()
            }}
        )

        print(f"❌ Summarization Failed: {service_id} -> {str(e)}")