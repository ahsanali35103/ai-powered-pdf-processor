import os
from pymongo import MongoClient
import gridfs
from dotenv import load_dotenv

load_dotenv()

MONGO_URI = os.getenv("MONGO_URI", "mongodb://127.0.0.1:27017/?replicaSet=rs0")
client = MongoClient(MONGO_URI, serverSelectionTimeoutMS=5000)
db = client["ai_pdf_processor"]

# Collections mapping Laravel collections
# VERIFIED: Laravel writes to 'services' (processing_requests has 0 docs, services has 49)
requests_collection = db["services"]
service_collection = requests_collection  # Alias for ocr_worker


files_collection = db["files"]
users_collection = db["users"]


# GridFS
fs = gridfs.GridFS(db)
