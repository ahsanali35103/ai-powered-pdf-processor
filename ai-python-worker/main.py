from pymongo import MongoClient
from pymongo.errors import ConnectionFailure
import os
from dotenv import load_dotenv
import sys

# Load environment variables
load_dotenv()

# ---- Check MongoDB Connection ----
def check_connection():
    try:
        MONGO_URI = os.getenv("MONGO_URI", "mongodb://localhost:27017/?replicaSet=rs0")
        client = MongoClient(MONGO_URI, serverSelectionTimeoutMS=5000)
        client.admin.command("ping")
        print("MongoDB Connected Successfully (Replica Set Active)")
        return True
    except Exception as e:
        print(f"MongoDB Connection Failed: {e}")
        return False


# ---- Main Entry Point ----
if __name__ == "__main__":
    print("PDF Processing Worker Started")

    if not check_connection():
        print("Critical Error: Could not connect to Database. Exiting...")
        sys.exit(1)

    try:
        import watcher_worker
        watcher_worker.watch()
    except KeyboardInterrupt:
        print("\nWorker Stopped Gracefully")
        sys.exit(0)