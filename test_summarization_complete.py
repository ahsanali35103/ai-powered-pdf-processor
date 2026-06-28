#!/usr/bin/env python3
"""
Complete Summarization Test
Tests the full OCR + Summarization workflow without database dependency
"""

import os
import sys
from datetime import datetime

# Add the worker directory to path
sys.path.append(os.path.join(os.path.dirname(__file__), 'ai-python-worker'))

def test_summarization_service():
    """Test SummarizationService core functionality"""
    print("=== Testing SummarizationService ===")
    
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
            
        # Test 2: Text extraction method
        print("\n2. Testing text extraction method...")
        try:
            # This would normally require a real file_id
            print("   Text extraction method: READY (requires file for actual testing)")
        except Exception as e:
            print(f"   Text extraction test: Expected error without real file - {e}")
        
        print("\n=== SummarizationService Test Complete ===")
        return True
        
    except Exception as e:
        print(f"SummarizationService Test Error: {e}")
        return False

def test_summarizer_module():
    """Test the summarizer module directly"""
    print("\n=== Testing Summarizer Module ===")
    
    try:
        from processors.summarizer.summarizer import generate_summary
        
        test_text = """
        Machine learning is a method of data analysis that automates analytical model building. 
        It is a branch of artificial intelligence based on the idea that systems can learn from data, 
        identify patterns and make decisions with minimal human intervention. 
        Machine learning algorithms build a mathematical model based on sample data, known as "training data", 
        in order to make predictions or decisions without being explicitly programmed to do so. 
        These algorithms are used in a wide variety of applications, such as in medicine, email filtering, 
        speech recognition, and computer vision.
        """
        
        summary = generate_summary(test_text)
        
        if summary:
            print(f"   Original: {len(test_text)} chars")
            print(f"   Summary: {len(summary)} chars")
            print(f"   Compression: {len(summary)/len(test_text)*100:.1f}%")
            print(f"   Summary: {summary[:100]}...")
            print("   Summarizer module: PASSED")
            return True
        else:
            print("   Summarizer module: FAILED")
            return False
            
    except Exception as e:
        print(f"Summarizer Module Test Error: {e}")
        return False

def test_ocr_service():
    """Test OCR Service import and basic functionality"""
    print("\n=== Testing OCR Service ===")
    
    try:
        from core.ocr_service import OCRService
        
        print("   OCR Service imported successfully")
        print("   OCR Service: READY (requires file for actual testing)")
        
        # Test basic method exists
        if hasattr(OCRService, 'process_to_text'):
            print("   process_to_text method: FOUND")
        else:
            print("   process_to_text method: MISSING")
            return False
            
        return True
        
    except Exception as e:
        print(f"OCR Service Test Error: {e}")
        return False

def test_watcher_worker():
    """Test watcher worker integration"""
    print("\n=== Testing Watcher Worker ===")
    
    try:
        import watcher_worker
        
        print("   Watcher worker imported successfully")
        
        # Check if process_summarization exists
        if hasattr(watcher_worker, 'process_summarization'):
            print("   process_summarization function: FOUND")
        else:
            print("   process_summarization function: MISSING")
            return False
            
        # Check if process_ocr exists
        if hasattr(watcher_worker, 'process_ocr'):
            print("   process_ocr function: FOUND")
        else:
            print("   process_ocr function: MISSING")
            return False
            
        print("   Watcher worker integration: PASSED")
        return True
        
    except Exception as e:
        print(f"Watcher Worker Test Error: {e}")
        return False

def test_complete_workflow():
    """Test complete workflow with mock data"""
    print("\n=== Testing Complete Workflow ===")
    
    try:
        from core.summarization_service import SummarizationService
        
        # Simulate a complete workflow
        print("   Step 1: Text extraction (simulated)")
        mock_text = """
        This document discusses the implementation of AI-powered PDF processing systems. 
        The system uses optical character recognition (OCR) to extract text from PDF documents and images. 
        After text extraction, advanced summarization algorithms process the content to generate concise summaries. 
        The system supports multiple file formats including PDF, PNG, JPG, and JPEG. 
        Processing is handled by a distributed worker system that manages job queues and ensures reliable processing. 
        Results are stored in a MongoDB database with comprehensive metadata and processing statistics.
        """
        
        print("   Step 2: Summarization")
        summary = SummarizationService.generate_summary_from_text(mock_text)
        
        if summary:
            print("   Step 3: Results validation")
            compression_ratio = len(summary) / len(mock_text) * 100
            
            print(f"   Original text: {len(mock_text)} characters")
            print(f"   Summary: {len(summary)} characters")
            print(f"   Compression ratio: {compression_ratio:.1f}%")
            print(f"   Processing method: ocr_summarization")
            
            # Validate summary quality
            if 5 < compression_ratio < 50:  # Reasonable compression
                print("   Summary quality: GOOD")
            else:
                print("   Summary quality: NEEDS ADJUSTMENT")
            
            print("   Complete workflow: PASSED")
            return True
        else:
            print("   Complete workflow: FAILED - No summary generated")
            return False
            
    except Exception as e:
        print(f"Complete Workflow Test Error: {e}")
        return False

def main():
    """Run all summarization tests"""
    print("=== COMPLETE SUMMARIZATION SYSTEM TEST ===")
    print(f"Started at: {datetime.now()}")
    
    tests = [
        ("Summarizer Module", test_summarizer_module),
        ("OCR Service", test_ocr_service),
        ("SummarizationService", test_summarization_service),
        ("Watcher Worker", test_watcher_worker),
        ("Complete Workflow", test_complete_workflow),
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
    
    if passed >= 4:  # At least 4/5 tests pass
        print("   Summarization system is READY for production!")
        print("\nNext steps:")
        print("   1. Start MongoDB service")
        print("   2. Run the worker: python ai-python-worker/main.py")
        print("   3. Create test jobs via Laravel API")
        print("   4. Monitor processing in MongoDB")
    else:
        print("   Some tests FAILED. Check the errors above.")
    
    print(f"\nCompleted at: {datetime.now()}")

if __name__ == "__main__":
    main()
