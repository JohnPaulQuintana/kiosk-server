<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Visit Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .content {
            margin-top: 10px;
            font-size: 16px;
            color: #555;
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            Teacher Visit Notification
        </div>
        <div class="content">
            <p>Dear {{ $teacher->name }},</p>
            <p>This is to inform you that a student or visitor is currently locating your unit: <strong>{{ $teacher->unit }}</strong>.</p>
            <p>The notification was triggered at: <strong>{{ date('Y-m-d H:i:s') }}</strong></p>
        </div>
        <div class="footer">
            <p>Thank you for using our application.</p>
        </div>
    </div>
</body>
</html>
