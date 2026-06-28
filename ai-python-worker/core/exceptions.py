class AppException(Exception):
    """
    Custom base exception for your whole project
    """

    def __init__(self, message, error_code=500):
        super().__init__(message)
        self.message = message
        self.error_code = error_code


# Wrapper for safe execution
def safe_execute(func):
    """
    Wrapper to handle exceptions globally.
    It catches custom AppExceptions and generic system exceptions.
    """

    def wrapper(*args, **kwargs):
        try:
            return func(*args, **kwargs)
        except AppException as e:
            # Handling our custom defined errors
            print(f" APP ERROR [{e.error_code}]: {e.message}")
            return None
        except Exception as e:
            # Handling any unexpected system errors
            print(f" GLOBAL ERROR: {str(e)}")
            return None

    return wrapper