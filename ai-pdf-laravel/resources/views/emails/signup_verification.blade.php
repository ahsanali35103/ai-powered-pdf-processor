<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background-color: #ffffff;
            color: #000000;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 450px;
            margin: 20px;
            border: 1px solid #000000;
            padding: 40px;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .icon-box {
            display: inline-block;
            border: 2px solid #000000;
            padding: 12px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }

        .content h2 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .content p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .code-container {
            background-color: #f9f9f9;
            border: 1px solid #000000;
            padding: 25px;
            text-align: center;
            margin-bottom: 30px;
        }

        .code-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            display: block;
        }

        .verification-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 8px;
        }

        .security-notice {
            border-top: 1px solid #000000;
            padding-top: 25px;
        }

        .security-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            display: block;
            margin-bottom: 5px;
        }

        .security-text {
            font-size: 12px;
            line-height: 1.4;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
            text-align: center;
        }

        .footer p {
            font-size: 10px;
            color: #666666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <div class="icon-box">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1>Verify Your Email</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ $name }},</h2>
            <p>
                Thank you for signing up. To complete your registration and secure your account, please use the verification code provided below.
            </p>
            
            <div class="code-container">
                <span class="code-label">Verification Code</span>
                <div class="verification-code">
                    {{ $verification_code }}
                </div>
            </div>
            
            <div class="security-notice">
                <span class="security-title">Important Notice</span>
                <p class="security-text">
                    This code is valid for 24 hours. If you did not create an account with us, please disregard this email.
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>Automated Message • Please do not reply</p>
        </div>
    </div>

</body>
</html>