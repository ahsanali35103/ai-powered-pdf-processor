import os

def file_exists(file_path):
    """
    Check if file exists and is a valid file (not a directory)
    """
    return file_path and os.path.isfile(file_path)


def read_text_file(file_path):
    """
    Read normal text file with error handling
    """
    try:
        with open(file_path, "r", encoding="utf-8") as f:
            return f.read()
    except Exception as e:
        print(f"Error reading file: {str(e)}")
        return None


def is_image_or_pdf(file_path):
    """
    Check file type for OCR (Case-insensitive check)
    """
    if not file_path:
        return False
    
    # .lower() ensure karta hai ke .PNG ya .JPG bhi accept ho jayen
    valid_extensions = (".png", ".jpg", ".jpeg", ".pdf")
    return file_path.lower().endswith(valid_extensions)