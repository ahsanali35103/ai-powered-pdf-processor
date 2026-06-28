"""
FCM Notification Sender (Polling-based) - DISABLED
This script previously polled for completed/failed jobs.
It has been disabled because notifications are now handled by MongoDB Change Streams 
via 'notification_watcher.py'.
"""
import sys

def main():
    print("=" * 50)
    print("  FCM Notification Polling System: [INACTIVE]")
    print("=" * 50)
    print("Polling disabled. Change Stream is handling notifications.")
    print("Please run 'notification_watcher.py' instead.")
    print("=" * 50)

if __name__ == "__main__":
    main()
