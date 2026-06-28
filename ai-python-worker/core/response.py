def success_response(data=None, message="Success", code=200):
    """
    Standardized success response format
    """
    return {
        "status": "success",
        "code": code,
        "message": message,
        "data": data
    }


def error_response(message="Error occurred", error_code=500):
    """
    Standardized error response format with error code
    """
    return {
        "status": "error",
        "code": error_code,
        "message": message,
        "data": None
    }