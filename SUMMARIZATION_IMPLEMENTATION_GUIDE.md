# Summarization Implementation Guide

## Overview
Complete OCR + Summarization pipeline that processes PDFs and images, extracts text using OCR, generates intelligent summaries, and updates MongoDB database.

## Architecture

### 1. Core Components

#### **SummarizationService** (`core/summarization_service.py`)
- **Purpose**: Main service handling complete summarization workflow
- **Features**:
  - OCR text extraction from PDFs and images
  - Intelligent text summarization
  - Database updates with results
  - Error handling and logging
  - File validation

#### **OCRService** (`core/ocr_service.py`)
- **Purpose**: Extract text from PDFs and images
- **Supports**: PDF, PNG, JPG, JPEG
- **Technology**: Tesseract OCR + PyMuPDF for PDFs

#### **Summarizer** (`processors/summarizer/summarizer.py`)
- **Purpose**: Generate concise summaries from text
- **Features**:
  - Sentence scoring algorithm
  - Keyword-based importance ranking
  - Configurable compression ratios
  - Text preprocessing

#### **Watcher Worker** (`watcher_worker.py`)
- **Purpose**: Process jobs from MongoDB queue
- **Integration**: Handles summarization jobs using SummarizationService

### 2. Workflow

```
1. User uploads file via Laravel API
2. Laravel creates summarization job in MongoDB
3. Watcher Worker picks up job
4. SummarizationService extracts text using OCR
5. Summarizer generates summary
6. MongoDB updated with results
7. Notification sent to user
```

## Usage

### **Direct Service Usage**

```python
from core.summarization_service import SummarizationService

# Complete workflow
result = SummarizationService.process_summarization(job_id, file_id)

# Text extraction only
text = SummarizationService.extract_text_for_summarization(file_id)

# Summary generation only
summary = SummarizationService.generate_summary_from_text(text)

# File validation
validation = SummarizationService.validate_file_for_summarization(file_id)
```

### **API Integration**

#### **Create Summarization Job**
```http
POST /api/service/create
{
    "file_id": "your_file_id",
    "type": "summarization"
}
```

#### **Check Job Status**
```http
GET /api/service/read?file_id=your_file_id
```

### **Worker Operation**

```bash
# Start the worker
cd ai-python-worker
python main.py

# Or run specific watcher
python notification_watcher.py
```

## Database Schema

### **Services Collection**
```javascript
{
  "_id": ObjectId("..."),
  "user_id": "user_uuid",
  "file_id": "file_uuid", 
  "type": "summarization",
  "status": "completed|failed|pending|processing",
  "output": {
    "extracted_text": "...",
    "summary": "...",
    "text_length": 5000,
    "summary_length": 500,
    "compression_ratio": 10.0,
    "processed_at": "2026-04-29T...",
    "processing_method": "ocr_summarization"
  },
  "created_at": "...",
  "completed_at": "..."
}
```

## Testing

### **Run Complete Test Suite**
```bash
cd AI-Powered-pdf-Processer
python test_summarization_pipeline.py
```

### **Test Individual Components**
```bash
# Test summarization service
python ai-python-worker/core/summarization_service.py

# Test summarizer module
python ai-python-worker/processors/summarizer/summarizer.py

# Test OCR service (requires file)
python -c "from core.ocr_service import OCRService; print('OCR Ready')"
```

## Configuration

### **Summarizer Settings** (`processors/summarizer/summarizer.py`)
```python
class SummarizerConfig:
    MIN_SUMMARY_LENGTH = 50      # Min chars in summary
    MAX_SUMMARY_LENGTH = 500     # Max chars in summary  
    TARGET_COMPRESSION = 0.3     # 30% of original length
    MIN_TEXT_LENGTH = 100        # Min text to summarize
```

### **OCR Settings** (`core/ocr_service.py`)
```python
tesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'
```

## File Support

### **Supported Formats**
- **PDF**: Multi-page documents with text extraction
- **Images**: PNG, JPG, JPEG with OCR processing
- **Size Limit**: 50MB per file

### **Processing Flow**
1. **PDF**: Convert pages to images, then OCR each page
2. **Images**: Direct OCR processing
3. **Text**: Clean and normalize for summarization

## Error Handling

### **Common Errors**
- **File not found**: Invalid file_id
- **Unsupported format**: File type not supported
- **OCR failure**: No text extracted
- **Summarization failure**: Text too short or processing error

### **Error Response**
```json
{
  "status": "failed",
  "output": {
    "error": "Error message",
    "processed_at": "2026-04-29T...",
    "processing_method": "ocr_summarization"
  }
}
```

## Monitoring

### **Logging**
All operations include detailed logging:
- `[SUMMARIZATION]` - Service operations
- `[OCR]` - Text extraction
- `[WATCHER]` - Job processing

### **Database Monitoring**
```python
# Check pending jobs
pending_jobs = requests_collection.find({"status": "pending", "type": "summarization"})

# Check completed jobs
completed_jobs = requests_collection.find({"status": "completed", "type": "summarization"})
```

## Performance

### **Expected Processing Times**
- **Small PDF (1-5 pages)**: 10-30 seconds
- **Large PDF (10+ pages)**: 30-60 seconds
- **Images**: 5-15 seconds per image
- **Summarization**: 1-5 seconds

### **Optimization Tips**
- Ensure Tesseract is properly installed
- Use high-quality images for better OCR
- Consider file size limits for processing

## Troubleshooting

### **Common Issues**

#### **1. OCR Not Working**
```bash
# Check Tesseract installation
tesseract --version

# Verify path in ocr_service.py
tesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'
```

#### **2. Database Connection**
```bash
# Check MongoDB connection
python -c "from core.db import requests_collection; print('DB OK')"
```

#### **3. Worker Not Processing**
```bash
# Check for pending jobs
python -c "from core.db import requests_collection; print(requests_collection.count_documents({'status': 'pending'}))"
```

### **Debug Mode**
Add debug logging to identify issues:
```python
import logging
logging.basicConfig(level=logging.DEBUG)
```

## Deployment

### **Production Setup**
1. Install dependencies: `pip install -r requirements.txt`
2. Configure MongoDB connection
3. Install Tesseract OCR
4. Set up environment variables
5. Start worker process

### **Environment Variables**
```bash
MONGO_URI=mongodb://localhost:27017/?replicaSet=rs0
FIREBASE_CREDS_PATH=/path/to/firebase.json
```

## Integration Examples

### **Laravel Controller Integration**
```php
// Create summarization job
$service = Service::create([
    'user_id' => $userId,
    'file_id' => $fileId,
    'type' => 'summarization',
    'status' => 'pending'
]);
```

### **Frontend Integration**
```javascript
// Upload file and create job
const formData = new FormData();
formData.append('file', file);

fetch('/api/file/upload', { method: 'POST', body: formData })
  .then(response => response.json())
  .then(data => {
    // Create summarization job
    return fetch('/api/service/create', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        file_id: data.file_id,
        type: 'summarization'
      })
    });
  });
```

---

## Summary

This implementation provides a complete, production-ready summarization system that:
- Handles both PDFs and images
- Uses advanced OCR for text extraction  
- Generates intelligent summaries
- Updates database automatically
- Includes comprehensive error handling
- Supports monitoring and debugging

The system is designed to work seamlessly with your existing Laravel API and MongoDB database while providing robust text processing capabilities.
