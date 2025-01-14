<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .email-container {
            background-color: #ffffff;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding: 10px 0;
            background-color: #4CAF50;
            color: #ffffff;
            border-radius: 8px 8px 0 0;
        }

        .content {
            padding: 20px;
            color: #333333;
            line-height: 1.6;
        }

        .content ul {
            padding-left: 20px;
        }

        .footer {
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #777777;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #4CAF50;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>Re-Schedule Details</h1>
        </div>
        <div class="content">
            <p>Hi {{ $user }},</p>
            <p>Your reservation has been moved. Below are the details of your scheduled service:</p>
            <ul>
                <li><strong>Service:</strong> {{ $schedule->service->name }}</li>
                <li><strong>Date:</strong> {{ date('F d, Y', strtotime($schedule->schedule->date_added)) }}</li>
                <li><strong>Start Time:</strong> {{ date('h:i a', strtotime($schedule->start_time)) }}</li>
                <li><strong>End Time:</strong> {{ date('h:i a', strtotime($schedule->end_time)) }}</li>
            </ul>
            <p>If you need to make any changes, feel free to contact us.</p>
            <a href="{{ url('/') }}" class="btn">Visit Our Website</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
