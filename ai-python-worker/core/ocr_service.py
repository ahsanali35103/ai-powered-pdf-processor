import io
import os
import fitz  # PyMuPDF
import pytesseract
from PIL import Image
from bson import ObjectId

from core.db import fs, files_collection
from core.exceptions import AppException


# -------------------------
# Tesseract Setup
# -------------------------
TESSERACT_PATH = r"C:\Program Files\Tesseract-OCR\tesseract.exe"

if os.path.exists(TESSERACT_PATH):
    pytesseract.pytesseract.tesseract_cmd = TESSERACT_PATH


# -------------------------
# OCR CORE SERVICE
# -------------------------
class OCRService:

    @staticmethod
    def process_to_text(file_id):
        """
        Main OCR function
        Input: file_id
        Output: extracted text
        """

        # 1. Fetch file metadata
        file_doc = files_collection.find_one({"_id": ObjectId(file_id)})

        if not file_doc:
            raise AppException("File not found")

        # 2. Fetch file from GridFS
        grid_out = fs.get(ObjectId(file_doc["gridfs_id"]))
        file_bytes = grid_out.read()

        # 3. Detect extension
        ext = file_doc.get("file_extension", "").lower().replace(".", "")

        # ---------------- PDF ----------------
        if ext == "pdf":
            text = ""
            pdf = fitz.open(stream=file_bytes, filetype="pdf")

            for page in pdf:
                pix = page.get_pixmap(matrix=fitz.Matrix(2, 2))
                img = Image.open(io.BytesIO(pix.tobytes("png")))
                text += pytesseract.image_to_string(img) + "\n"

            pdf.close()
            return text.strip()

        # ---------------- IMAGE ----------------
        elif ext in ["png", "jpg", "jpeg"]:
            img = Image.open(io.BytesIO(file_bytes))
            return pytesseract.image_to_string(img).strip()

        # ---------------- ERROR ----------------
        else:
            raise AppException(f"Unsupported file type: {ext}")