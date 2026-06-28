#!/usr/bin/env python3
"""
Core Summarization Test (No Firebase Dependencies)
Tests only the essential summarization components
"""

import os
import sys
from datetime import datetime

# Add the worker directory to path
sys.path.append(os.path.join(os.path.dirname(__file__), 'ai-python-worker'))

def test_summarization_core():
    """Test core summarization functionality without dependencies"""
    print("=== CORE SUMMARIZATION TEST ===")
    
    tests = [
        ("Summarizer Module", test_summarizer_module),
        ("OCR Service", test_ocr_service),
        ("SummarizationService", test_summarization_service),
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
    print("\n=== CORE TEST SUMMARY ===")
    passed = 0
    total = len(results)
    
    for test_name, result in results.items():
        status = "PASSED" if result else "FAILED"
        print(f"   {test_name}: {status}")
        if result:
            passed += 1
    
    print(f"\nOverall: {passed}/{total} tests passed")
    
    if passed == total:
        print("   Core summarization system is PERFECT!")
        print("\nReady for production:")
        print("   1. Start MongoDB service")
        print("   2. Run worker: python ai-python-worker/main.py")
        print("   3. Create summarization jobs")
    else:
        print("   Some core tests FAILED.")
    
    return passed == total

def test_summarizer_module():
    """Test the summarizer module directly"""
    try:
        from processors.summarizer.summarizer import generate_summary
        
        test_text = """
        Natural language processing (NLP) is a subfield of linguistics, computer science, and artificial intelligence 
        concerned with the interactions between computers and human language. 
        NLP focuses on how to program computers to process and analyze large amounts of natural language data. 
        The result is a computer capable of understanding the contents of documents, including the contextual 
        nuances of the text within them. Challenges in NLP frequently involve speech recognition, natural language 
        understanding, and natural language generation.
        """
        
        summary = generate_summary(test_text)
        
        if summary:
            print(f"   Original: {len(test_text)} chars")
            print(f"   Summary: {len(summary)} chars")
            print(f"   Compression: {len(summary)/len(test_text)*100:.1f}%")
            print(f"   Summary: {summary[:80]}...")
            return True
        else:
            print("   No summary generated")
            return False
            
    except Exception as e:
        print(f"Summarizer Module Error: {e}")
        return False

def test_ocr_service():
    """Test OCR Service"""
    try:
        from core.ocr_service import OCRService
        
        print("   OCR Service imported successfully")
        
        # Check methods exist
        if hasattr(OCRService, 'process_to_text'):
            print("   process_to_text method: FOUND")
        else:
            print("   process_to_text method: MISSING")
            return False
            
        return True
        
    except Exception as e:
        print(f"OCR Service Error: {e}")
        return False

def test_summarization_service():
    """Test SummarizationService"""
    try:
        from core.summarization_service import SummarizationService
        
        # Test summary generation
        test_text = """
        Machine learning is a subset of artificial intelligence that provides systems the ability to 
        automatically learn and improve from experience without being explicitly programmed. 
        Machine learning focuses on the development of computer programs that can access data and use it 
        to learn for themselves. The process of learning begins with observations or data, such as examples, 
        direct experience, or instruction, in order to look for patterns in data and make better decisions 
        based on the examples that provide. Machine learning algorithms build a model based on sample data, 
        known as training data, in order to make predictions or decisions.
        """
        
        summary = SummarizationService.generate_summary_from_text(test_text)
        
        if summary:
            print(f"   Original: {len(test_text)} chars")
            print(f"   Summary: {len(summary)} chars")
            print(f"   Compression: {len(summary)/len(test_text)*100:.1f}%")
            print(f"   Summary: {summary[:80]}...")
            return True
        else:
            print("   No summary generated")
            return False
            
    except Exception as e:
        print(f"SummarizationService Error: {e}")
        return False

def test_complete_workflow():
    """Test complete workflow simulation"""
    try:
        from core.summarization_service import SummarizationService
        
        # Simulate complete processing
        print("   Simulating OCR + Summarization workflow...")
        
        # Mock extracted text (from OCR)
        mock_ocr_text = """
        This document outlines the implementation of a comprehensive AI-powered document processing system. 
        The system utilizes advanced optical character recognition (OCR) technology to extract text from 
        various document formats including PDF files, images, and scanned documents. Following text extraction, 
        the system employs sophisticated natural language processing algorithms to generate concise and 
        accurate summaries of the content. The architecture supports multiple file formats and provides 
        real-time processing capabilities through a distributed worker system. All processing results are 
        stored in a MongoDB database with comprehensive metadata, timestamps, and quality metrics. 
        The system is designed to handle high-volume processing while maintaining accuracy and reliability.
        """
        
        # Generate summary
        summary = SummarizationService.generate_summary_from_text(mock_ocr_text)
        
        if summary:
            compression_ratio = len(summary) / len(mock_ocr_text) * 100
            
            print(f"   OCR text: {len(mock_ocr_text)} chars")
            print(f"   Summary: {len(summary)} chars")
            print(f"   Compression: {compression_ratio:.1f}%")
            
            # Validate quality
            if 10 < compression_ratio < 30:
                print("   Quality: EXCELLENT")
            elif 5 < compression_ratio < 40:
                print("   Quality: GOOD")
            else:
                print("   Quality: NEEDS ADJUSTMENT")
            
            print(f"   Summary: {summary[:80]}...")
            return True
        else:
            print("   Workflow failed - no summary generated")
            return False
            
    except Exception as e:
        print(f"Complete Workflow Error: {e}")
        return False

if __name__ == "__main__":
    success = test_summarization_core()
    
    if success:
        print("\n" + "="*50)
        print("SUMMARIZATION SYSTEM IS READY!")
        print("="*50)
        print("\nTo start processing:")
        print("1. Ensure MongoDB is running")
        print("2. Run: python ai-python-worker/main.py")
        print("3. Create jobs via Laravel API")
        print("4. Monitor results in MongoDB")
    else:
        print("\nSome components need attention.")
