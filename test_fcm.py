#!/usr/bin/env python3
"""
FCM Notification System Test Script
Tests the complete FCM notification workflow
"""

import os
import sys
from datetime import datetime

# Add the worker directory to path
sys.path.append(os.path.join(os.path.dirname(__file__), 'ai-python-worker'))

try:
    from core.notifier import send_notification, notify_processing_complete
    from core.db import users_collection, services_collection
    print("✅ Successfully imported FCM modules")
except ImportError as e:
    print(f"❌ Import error: {e}")
    print("Make sure the Python worker dependencies are installed")
    sys.exit(1)

def test_firebase_connection():
    """Test Firebase initialization"""
    print("\n=== Testing Firebase Connection ===")
    
    try:
        import firebase_admin
        if firebase_admin._apps:
            print("✅ Firebase Admin SDK initialized successfully")
            return True
        else:
            print("❌ Firebase Admin SDK not initialized")
            return False
    except Exception as e:
        print(f"❌ Firebase connection error: {e}")
        return False

def test_user_fcm_tokens():
    """Test if users have FCM tokens"""
    print("\n=== Testing User FCM Tokens ===")
    
    try:
        # Find users with FCM tokens
        users_with_tokens = list(users_collection.find({"fcm_token": {"$exists": True, "$ne": ""}}))
        
        print(f"Found {len(users_with_tokens)} users with FCM tokens")
        
        for user in users_with_tokens:
            print(f"  - User ID: {user.get('_id')}")
            print(f"    Email: {user.get('email')}")
            print(f"    Token: {user.get('fcm_token', 'N/A')[:20]}...")
        
        return users_with_tokens
    except Exception as e:
        print(f"❌ Error checking user tokens: {e}")
        return []

def test_send_notification():
    """Test sending a notification"""
    print("\n=== Testing Notification Sending ===")
    
    # Get a user with FCM token
    users = test_user_fcm_tokens()
    
    if not users:
        print("❌ No users with FCM tokens found")
        return False
    
    test_user = users[0]
    user_id = test_user.get('_id')
    
    print(f"Testing notification to user: {user_id}")
    
    try:
        result = send_notification(
            user_id=user_id,
            title="Test Notification",
            body="This is a test notification from the AI PDF Processor",
            data={"test": True, "timestamp": datetime.utcnow().isoformat()}
        )
        
        if result:
            print("✅ Test notification sent successfully")
            return True
        else:
            print("❌ Test notification failed")
            return False
            
    except Exception as e:
        print(f"❌ Error sending test notification: {e}")
        return False

def test_service_completion_notification():
    """Test service completion notification"""
    print("\n=== Testing Service Completion Notification ===")
    
    # Find a recent completed service
    try:
        completed_service = services_collection.find_one({"status": "completed"})
        
        if not completed_service:
            print("❌ No completed services found")
            return False
        
        user_id = completed_service.get("user_id")
        service_type = completed_service.get("type")
        file_id = completed_service.get("file_id")
        
        print(f"Testing service completion notification:")
        print(f"  - User ID: {user_id}")
        print(f"  - Service Type: {service_type}")
        print(f"  - File ID: {file_id}")
        
        result = notify_processing_complete(
            user_id=user_id,
            service_type=service_type,
            file_id=file_id,
            success=True
        )
        
        if result:
            print("✅ Service completion notification sent successfully")
            return True
        else:
            print("❌ Service completion notification failed")
            return False
            
    except Exception as e:
        print(f"❌ Error testing service completion notification: {e}")
        return False

def main():
    """Run all FCM tests"""
    print("🔔 FCM Notification System Test")
    print("=" * 50)
    
    # Test 1: Firebase Connection
    if not test_firebase_connection():
        print("\n❌ Firebase connection failed. Check credentials file.")
        return
    
    # Test 2: User FCM Tokens
    users = test_user_fcm_tokens()
    if not users:
        print("\n⚠️  No users with FCM tokens. Use the frontend to generate and save tokens first.")
        print("   Visit: http://your-domain/firebase-test.html")
        return
    
    # Test 3: Send Test Notification
    test_send_notification()
    
    # Test 4: Service Completion Notification
    test_service_completion_notification()
    
    print("\n" + "=" * 50)
    print("🎯 FCM Testing Complete!")
    print("\nNext Steps:")
    print("1. Ensure Firebase credentials file is in place")
    print("2. Generate FCM tokens using the frontend")
    print("3. Create and complete services to trigger notifications")

if __name__ == "__main__":
    main()
