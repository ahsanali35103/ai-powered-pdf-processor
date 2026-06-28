from core.db import requests_collection
from core.ocr_service import OCRService
from processors.Translator.translate import translate_text
from core.response import success_response, error_response
from core.languages import SUPPORTED_LANGUAGES


def run_service(service):

    print("Processing...")

    service_id = service["_id"]
    target = service["target_language"]

    try:
        if target not in SUPPORTED_LANGUAGES:
            raise Exception("Unsupported language")

        requests_collection.update_one(
            {"_id": service_id},
            {"$set": {"status": "processing"}}
        )

        file_id = service.get("file_id")
        if not file_id:
            raise Exception("file_id missing")

        text = OCRService.process_to_text(file_id)

        if not text:
            raise Exception("No text extracted")

        translated = translate_text(text, target)

        output = success_response({
            "translated_text": translated
        })

        requests_collection.update_one(
            {"_id": service_id},
            {"$set": {
                "status": "completed",
                "output": output
            }}
        )

        print("Completed:", service_id)

    except Exception as e:

        requests_collection.update_one(
            {"_id": service_id},
            {"$set": {
                "status": "failed",
                "output": error_response(str(e))
            }}
        )

        print("Failed:", service_id, e)
