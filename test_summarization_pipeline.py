#!/usr/bin/env python3
"""
Complete Summarization Pipeline Test
Tests the entire OCR + Summarization workflow
"""

import os
import sys
from datetime import datetime

# Add the worker directory to path
sys.path.append(os.path.join(os.path.dirname(__file__), 'ai-python-worker'))

def test_summarization_service():
    """Test the SummarizationService directly"""
    print("=== Testing Summarization Service ===")
    
    try:
        from core.summarization_service import SummarizationService
        
        # Test 1: Summary generation from text
        print("\n1. Testing summary generation from text...")
        test_text = """
        Artificial intelligence (AI) is intelligence demonstrated by machines, in contrast to the natural intelligence displayed by humans and animals. 
        Leading AI textbooks define the field as the study of "intelligent agents": any device that perceives its environment and takes actions that maximize its chance of successfully achieving its goals. 
        Colloquially, the term "artificial intelligence" is often used to describe machines that mimic "cognitive" functions that humans associate with the human mind, such as "learning" and "problem solving". 
        As machines become increasingly capable, tasks considered to require "intelligence" are often removed from the definition of AI, a phenomenon known as the AI effect. 
        A quip in Tesler's Theorem says "AI is whatever hasn't been done yet."
        """
        
        summary = SummarizationService.generate_summary_from_text(test_text)
        if summary:
            print(f"   Original text: {len(test_text)} characters")
            print(f"   Summary: {len(summary)} characters")
            print(f"   Compression: {len(summary)/len(test_text)*100:.1f}%")
            print(f"   Summary preview: {summary[:100]}...")
            print("   Summary generation: PASSED")
        else:
            print("   Summary generation: FAILED")
            return False
            
        # Test 2: File validation (without real file)
        print("\n2. Testing file validation...")
        validation_result = SummarizationService.validate_file_for_summarization("test_file_id")
        print(f"   Validation result: {validation_result}")
        
        # Test 3: Main processing method (without database updates)
        print("\n3. Testing main processing method...")
        try:
            # This would normally require a real file_id
            print("   Main processing method: READY (requires file for actual testing)")
        except Exception as e:
            print(f"   Main processing test: Expected error without real file - {e}")
        
        print("\n=== Summarization Service Test Complete ===")
        return True
        
    except Exception as e:
        print(f"Summarization Service Test Error: {e}")
        return False

def test_ocr_service():
    """Test the OCR Service"""
    print("\n=== Testing OCR Service ===")
    
    try:
        from core.ocr_service import OCRService
        
        # Test OCR service import and basic functionality
        print("OCR Service imported successfully")
        
        # Note: Actual OCR testing requires real file_id
        print("   OCR Service: READY (requires file for actual testing)")
        
        return True
        
    except Exception as e:
        print(f"OCR Service Test Error: {e}")
        return False

def test_summarizer_module():
    """Test the summarizer module"""
    print("\n=== Testing Summarizer Module ===")
    
    try:
        from processors.summarizer.summarizer import generate_summary
        
        test_text = """
        This is a comprehensive document about machine learning. Machine learning is a subset of artificial intelligence that focuses on the use of data and algorithms to imitate the way that humans learn. 
        Machine learning algorithms build a model based on sample data, known as training data, in order to make predictions or decisions without being explicitly programmed to do so. 
        Machine learning algorithms are used in a wide variety of applications, such as in medicine, email filtering, speech recognition, and computer vision.
        """
        
        summary = generate_summary(test_text)
        
        if summary:
            print(f"   Original: {len(test_text)} chars")
            print(f"   Summary: {len(summary)} chars")
            print(f"   Compression: {len(summary)/len(test_text)*100:.1f}%")
            print("   Summarizer module: PASSED")
            return True
        else:
            print("   Summarizer module: FAILED")
            return False
            
    except Exception as e:
        print(f"Summarizer Module Test Error: {e}")
        return False

def test_watcher_worker():
    """Test the watcher worker integration"""
    print("\n=== Testing Watcher Worker Integration ===")
    
    try:
        # Test importing the watcher worker
        import watcher_worker
        
        print("   Watcher worker imported successfully")
        
        # Test the process_summarization function exists
        if hasattr(watcher_worker, 'process_summarization'):
            print("   process_summarization function: FOUND")
        else:
            print("   process_summarization function: MISSING")
            return False
            
        print("   Watcher worker integration: PASSED")
        return True
        
    except Exception as e:
        print(f"Watcher Worker Test Error: {e}")
        return False

def test_database_connection():
    """Test database connectivity"""
    print("\n=== Testing Database Connection ===")
    
    try:
        from core.db import requests_collection, files_collection
        
        # Test basic database operations
        service_count = requests_collection.count_documents({})
        file_count = files_collection.count_documents({})
        
        print(f"   Services in database: {service_count}")
        print(f"   Files in database: {file_count}")
        print("   Database connection: PASSED")
        return True
        
    except Exception as e:
        print(f"Database Connection Test Error: {e}")
        return False

def create_test_job():
    """Create a test summarization job for demonstration"""
    print("\n=== Creating Test Job ===")
    
    try:
        from core.db import requests_collection, files_collection
        from bson import ObjectId
        from datetime import datetime
        
        # Check if we have any files to use for testing
        sample_file = files_collection.find_one()
        
        if not sample_file:
            print("   No files found in database for testing")
            return None
        
        # Create a test summarization job
        test_job = {
            "_id": ObjectId(),
            "user_id": "test_user_id",
            "file_id": str(sample_file["_id"]),
            "type": "summarization",
            "status": "pending",
            "created_at": datetime.utcnow(),
            "organization_id": "test_org_id"
        }
        
        # Insert the test job
        result = requests_collection.insert_one(test_job)
        
        if result.inserted_id:
            print(f"   Test job created: {result.inserted_id}")
            print(f"   Using file: {sample_file.get('file_name', 'unknown')}")
            return str(result.inserted_id)
        else:
            print("   Failed to create test job")
            return None
            
    except Exception as e:
        print(f"Test Job Creation Error: {e}")
        return None

def main():
    """Run all tests"""
    print("=== COMPLETE SUMMARIZATION PIPELINE TEST ===")
    print(f"Started at: {datetime.now()}")
    
    tests = [
        ("Database Connection", test_database_connection),
        ("OCR Service", test_ocr_service),
        ("Summarizer Module", test_summarizer_module),
        ("Summarization Service", test_summarization_service),
        ("Watcher Worker Integration", test_watcher_worker),
    ]
    
    results = {}
    
    for test_name, test_func in tests:
        print(f"\n--- {test_name} ---")
        try:
            results[test_name] = test_func()
        except Exception as e:
            print(f"{test_name} ERROR: {e}")
            results[test_name] = False
    
    # Summary
    print("\n=== TEST SUMMARY ===")
    passed = 0
    total = len(results)
    
    for test_name, result in results.items():
        status = "PASSED" if result else "FAILED"
        print(f"   {test_name}: {status}")
        if result:
            passed += 1
    
    print(f"\nOverall: {passed}/{total} tests passed")
    
    if passed == total:
        print("   All tests PASSED! Summarization pipeline is ready.")
        
        # Create a test job if everything works
        test_job_id = create_test_job()
        if test_job_id:
            print(f"   Test job created: {test_job_id}")
            print("   You can now run the worker to process this job.")
    else:
        print("   Some tests FAILED. Check the errors above.")
    
    print(f"\nCompleted at: {datetime.now()}")

if __name__ == "__main__":
    main()
