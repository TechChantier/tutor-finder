<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Connection Request</title>
</head>
<body>
    <h1>Hello {{ $tutorName }},</h1>
    <p>You have received a new connection request from <strong>{{ $learnerName }}</strong>.</p>
    <p><strong>Message:</strong> {{ $message }}</p>
    <p><strong>Period:</strong> {{ ucfirst($periodType) }}</p>
    <p><strong>Amount:</strong> {{ $amountPaid }}</p>
    <p>Please log in to your dashboard to review this request:</p>
    <a href="{{ $dashboardUrl }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
        Go to Dashboard
    </a>
    <p>Thank you,</p>
    <p><strong>Tutor Finder Team</strong></p>
</body>
</html>