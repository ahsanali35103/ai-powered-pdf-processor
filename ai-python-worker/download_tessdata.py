import urllib.request
import os

print("Downloading eng.traineddata...")
url = "https://github.com/tesseract-ocr/tessdata_fast/raw/main/eng.traineddata"
tessdata_dir = os.path.join(os.getcwd(), "tessdata")
os.makedirs(tessdata_dir, exist_ok=True)
path = os.path.join(tessdata_dir, "eng.traineddata")

try:
    urllib.request.urlretrieve(url, path)
    print("Download completed successfully!")
except Exception as e:
    print(f"Download failed: {e}")
