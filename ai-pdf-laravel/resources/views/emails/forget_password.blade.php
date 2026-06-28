<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        /* Base Styles */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #ffffff;
            color: #000000;
            margin: 0;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        /* Container */
        .main-container {
            width: 100%;
            max-width: 500px;
            border: 1px solid #000000;
            padding: 40px;
            box-sizing: border-box;
        }

        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 35px;
        }

        .icon-wrapper {
            display: inline-block;
            border: 2px solid #000000;
            padding: 15px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 22px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }

        /* Content Section */
        .content h2 {
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 12px 0;
        }

        .content p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 25px;
            color: #333333;
        }

        /* Code Box */
        .code-box {
            background-color: #f9f9f9;
            border: 1px solid #000000;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
        }

        .code-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: block;
            margin-bottom: 12px;
        }

        .reset-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 10px;
        }

        /* Security Notice */
        .security-notice {
            border-top: 1px solid #000000;
            padding-top: 25px;
        }

        .security-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            display: block;
            margin-bottom: 8px;
        }

        .security-text {
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
            text-align: center;
        }

        .footer p {
            font-size: 10px;
            color: #888888;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            .main-container {
                padding: 25px;
            }
            .reset-code {
                font-size: 24px;
                letter-spacing: 6px;
            }
        }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="header">
            <div class="icon-wrapper">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0110 0v4"></path>
                </svg>
            </div>
            <h1>Reset Password</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ $name }},</h2>
            <p>
                We received a request to reset your password. Please use the following code to complete the security process.
            </p>
            
            <div class="code-box">
                <span class="code-label">Verification Code</span>
                <div class="reset-code">
                    {{ $reset_code }}
                </div>
            </div>
            
            <div class="security-notice">
                <span class="security-label">Security Notice</span>
                <p class="security-text">
                    This code is valid for 2 hours. If you did not request a password reset, no action is required and you can safely delete this email.
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>Automated Security Message • Do Not Reply</p>
        </div>
    </div>

</body>
</html>