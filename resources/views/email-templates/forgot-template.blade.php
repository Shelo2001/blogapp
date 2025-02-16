<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 12px 20px;
            margin: 20px 0;
            text-decoration: none;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 5px;
            font-size: 16px;
        }
        .footer {
            font-size: 12px;
            color: #666;
            margin-top: 20px;
        }
        @media (max-width: 600px) {
            .container {
                width: 90%;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h4>Password Reset Request, {{$user->name}}</h4>
        <p>You requested to reset your password. Click the button below to proceed.</p>
        <a href="{{$actionLink}}" target="_blank" class="button">Reset Password</a>
        <p>If you didn't request this, you can ignore this email.</p>
        <p class="footer">&copy; 2025. All rights reserved.</p>
    </div>
</body>
</html>