# AI PDF Processing - Postman Testing Guide

## 📋 Overview

This guide provides comprehensive instructions for testing both the Laravel API and Python Worker using Postman collections.

## 🚀 Quick Setup

### 1. Import Collections and Environment

1. **Import Collections:**
   - `Laravel_API_Collection.json` - Main Laravel API endpoints
   - `Python_Worker_Collection.json` - Python worker testing endpoints

2. **Import Environment:**
   - `Environment_Variables.json` - Pre-configured environment variables

3. **Select Environment:**
   - Choose "AI PDF Processing Environment" in Postman

### 2. Update Environment Variables

Before testing, update these variables in your environment:

```json
{
  "base_url": "http://localhost:8000",           // Laravel API URL
  "python_worker_url": "http://localhost:5000",  // Python worker URL (if applicable)
  "mongo_url": "mongodb://localhost:27017",      // MongoDB connection
  "organization_name": "Your Organization",      // Your organization name
  "test_email": "your.email@example.com",       // Your test email
  "test_password": "YourSecurePassword123!"     // Your test password
}
```

## 🔐 Authentication Flow Testing

### Step 1: User Registration
```
POST /api/auth/signup
```
**Body (JSON):**
```json
{
    "name": "{{test_name}}",
    "email": "{{test_email}}",
    "password": "{{test_password}}",
    "confirm_password": "{{test_password}}"
}
```

### Step 2: Email Verification
```
POST /api/auth/verify-signup
```
**Body (JSON):**
```json
{
    "email": "{{test_email}}",
    "token": "VERIFICATION_TOKEN_FROM_EMAIL"
}
```

### Step 3: Login (Auto-saves token)
```
POST /api/auth/login
```
**Body (JSON):**
```json
{
    "email": "{{test_email}}",
    "password": "{{test_password}}"
}
```
*Note: Access token is automatically saved to environment variables*

## 📁 File Management Testing

### Step 1: Upload File (Auto-saves file_id)
```
POST /api/file/upload
```
**Body (Form-data):**
- `file`: Select a PDF or image file (max 20MB)
- `user_id`: `{{user_id}}`

**Supported formats:** PDF, JPG, JPEG, PNG

### Step 2: Delete File
```
DELETE /api/file/delete
```
**Body (JSON):**
```json
{
    "file_id": "{{file_id}}",
    "user_id": "{{user_id}}"
}
```

## 🔄 Service Processing Testing

### Step 1: Create OCR Service
```
POST /api/service/create
```
**Body (JSON):**
```json
{
    "user_id": "{{user_id}}",
    "organization_name": "{{organization_name}}",
    "file_id": "{{file_id}}",
    "type": "ocr"
}
```

### Step 2: Create Summarization Service
```
POST /api/service/create
```
**Body (JSON):**
```json
{
    "user_id": "{{user_id}}",
    "organization_name": "{{organization_name}}",
    "file_id": "{{file_id}}",
    "type": "summarization"
}
```

### Step 3: Create Translation Service
```
POST /api/service/create
```
**Body (JSON):**
```json
{
    "user_id": "{{user_id}}",
    "organization_name": "{{organization_name}}",
    "file_id": "{{file_id}}",
    "type": "translation",
    "target_language": "spanish"
}
```

### Step 4: Check Service Results
```
GET /api/service/read?file_id={{file_id}}&user_id={{user_id}}
```

### Step 5: List All User Services
```
GET /api/service/list?user_id={{user_id}}&organization_name={{organization_name}}
```

**Optional filters:**
- `&type=ocr` - Filter by service type
- `&status=completed` - Filter by status

## 🐍 Python Worker Testing

### Prerequisites
- Python worker must be running
- MongoDB must be accessible
- Files must be uploaded via Laravel API first

### Test Worker Status
```
GET {{python_worker_url}}/status
```

### Test Direct Processing
```
POST {{python_worker_url}}/process/ocr
```
**Body (JSON):**
```json
{
    "file_id": "{{file_id}}",
    "type": "ocr"
}
```

## 📊 Expected Response Formats

### Success Response
```json
{
    "success": true,
    "message": "Operation successful",
    "errors": null,
    "data": {
        // Response data here
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error description",
    "errors": ["Detailed error messages"],
    "data": null
}
```

## 🔍 Testing Scenarios

### 1. Complete Workflow Test
1. Register user → Verify email → Login
2. Upload file → Create OCR service → Check results
3. Create summarization service → Check results
4. Create translation service → Check results
5. List all services → Verify data

### 2. Error Handling Test
1. Try uploading invalid file format
2. Try accessing other user's files
3. Try creating service without required fields
4. Test rate limiting (multiple rapid requests)

### 3. Security Test
1. Try accessing endpoints without authentication
2. Try accessing other user's data
3. Test file size limits
4. Test malicious file uploads

## 🚨 Common Issues & Solutions

### Issue: "Unauthorized" Error
**Solution:** Ensure you've logged in and access token is saved to environment

### Issue: "File not found" Error
**Solution:** Upload a file first and ensure file_id is saved to environment

### Issue: "Validation failed" Error
**Solution:** Check request body format and required fields

### Issue: Connection refused
**Solution:** Ensure Laravel server is running on correct port (php artisan serve)

## 📝 Test Data Examples

### Sample User Data
```json
{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "password": "SecurePass123!",
    "confirm_password": "SecurePass123!"
}
```

### Sample Service Creation
```json
{
    "user_id": "user_12345",
    "organization_name": "Acme Corp",
    "file_id": "file_67890",
    "type": "translation",
    "target_language": "french"
}
```

### Supported Languages for Translation
- english, spanish, french, german, italian, portuguese, russian, chinese, japanese, korean

## 🔧 Environment Setup Commands

### Start Laravel Server
```bash
cd ai-pdf-laravel
php artisan serve
```

### Start Python Worker (if applicable)
```bash
cd ai-python-worker
python main.py
```

### Start MongoDB
```bash
mongod --dbpath /path/to/data/directory
```

## 📈 Performance Testing

### Load Testing Endpoints
1. File upload with large files (up to 20MB)
2. Multiple concurrent service requests
3. Bulk service listing with filters
4. Rate limiting verification

### Monitoring Points
- Response times for each endpoint
- File upload/download speeds
- Service processing times
- Error rates and types

---

**Happy Testing! 🎉**

For issues or questions, check the API documentation or contact the development team.