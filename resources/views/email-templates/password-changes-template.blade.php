<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Changed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            padding: 20px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        p {
            font-size: 16px;
            color: #555;
        }
        .info {
            background: #eee;
            padding: 10px;
            border-radius: 5px;
            word-break: break-word;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Password Changed Successfully</h2>
        <p>Hello, {{$user->name}}</p>
        <p>Your password has been successfully changed. Below are your updated login details:</p>
        <div class="info">
            <p><strong>Email:</strong>{{$user->email}}</p>
            <p><strong>New Password:</strong> {{$new_password}}</p>
        </div>
        <p>If you did not request this change, please contact our support team immediately.</p>
        <p class="footer">Thank you,<br> The Support Team</p>
    </div>
</body>
</html>