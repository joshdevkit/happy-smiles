<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow-Up Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            max-width: 600px;
            margin: 30px auto;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #4CAF50;
            color: #fff;
            padding: 15px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            padding: 20px;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h2>Follow-Up Reminder</h2>
        </div>
        <div class="content">
            <p>Hi {{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }},</p>

            <p>We are reminding you about your upcoming follow-up appointment scheduled for:</p>

            <p>
                <strong>Service: {{ $followup['service'] }}</strong><br>
                <strong>Date:</strong> {{ $followup['date'] }}<br>
                <strong>Time:</strong> {{ $followup['start_time'] }} - {{ $followup['end_time'] }}
            </p>

            <p>If you need to reschedule or have any questions, feel free to contact us.</p>

            <a href="{{ url('/') }}" class="btn">Visit Our Website</a>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Happy Smile Dental Clinic. All rights reserved.</p>
        </div>
    </div>

</body>

</html>
