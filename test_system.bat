@echo off
echo.
echo ========================================
echo AI-PDF Processing System - Full Test
echo ========================================
echo.
echo [1] Testing MongoDB Connection...
cd /d C:\Users\ahsan\Desktop\newproject\AI-Powered-pdf-Processer\ai-python-worker
python -c "from core.db import client; client.admin.command('ping'); print('[OK] MongoDB Connected')" 2>nul || echo [FAIL] MongoDB not responding

echo.
echo [2] Creating test PDF file...
python -c "
from reportlab.pdfgen import canvas
from reportlab.lib.pagesizes import letter
import os

pdf_path = 'test_sample.pdf'
c = canvas.Canvas(pdf_path, pagesize=letter)
c.setFont('Helvetica', 12)
c.drawString(100, 750, 'Test PDF for OCR Processing')
c.drawString(100, 730, 'This is a sample document for testing.')
c.drawString(100, 710, 'Lorem ipsum dolor sit amet.')
c.save()
print(f'[OK] Created: {pdf_path}')
" 2>nul || echo [NOTE] Install reportlab to auto-create PDF

echo.
echo [3] Checking Laravel on port 8000...
for /f %%A in ('powershell -Command "(New-Object Net.WebClient).DownloadString('http://127.0.0.1:8000/') | Select-String -Pattern 'Laravel' | Measure-Object -Line | Select-Object -ExpandProperty Lines" 2^>nul') do (
    if %%A GTR 0 (echo [OK] Laravel is running) else (echo [WARN] Laravel response unclear)
)

echo.
echo [4] Checking OCR Worker status...
tasklist /FI "IMAGENAME eq python.exe" | find /I "python" >nul && echo [OK] Python worker is running || echo [WARN] Python worker may not be running

echo.
echo [5] System Status Summary:
echo - MongoDB: Check terminal "mongod"
echo - Laravel: Check terminal with "setup_and_run_laravel.bat"  
echo - OCR Worker: Check terminal with "start_ocr_worker.bat"
echo.
echo ========================================
echo Setup Complete! System Ready for Testing
echo ========================================
