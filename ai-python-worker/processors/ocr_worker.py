from datetime import datetime
from core.ocr_service import OCRService
from core.db import service_collection
from core.exceptions import safe_execute
import pytesseract
pytesseract.pytesseract.tesseract_cmd = r"C:\Program Files\Tesseract-OCR\tesseract.exe"

class OCRWorker:

    def __init__(self):
        self.db = service_collection

    @safe_execute
    def run_ocr(self, doc):

        service_id = doc.get("_id")
        file_id = doc.get("file_id")

        # Update status → processing
        self.db.update_one(
            {"_id": service_id},
            {"$set": {"status": "processing"}}
        )

        try:
            # ✅ MAIN OCR CALL
            text = OCRService.process_to_text(file_id)

            # Save result
            self.db.update_one(
                {"_id": service_id},
                {"$set": {
                    "status": "success",
                    "output": {
                        "text": text,
                        "char_count": len(text),
                        "processed_at": datetime.utcnow().isoformat()
                    },
                    "completed_at": datetime.utcnow()
                }}
            )

            print(f"[OCR SUCCESS] {service_id}")

        except Exception as e:

            self.db.update_one(
                {"_id": service_id},
                {"$set": {
                    "status": "failed",
                    "error": str(e),
                    "completed_at": datetime.utcnow()
                }}
            )

            print(f"[OCR ERROR] {e}")