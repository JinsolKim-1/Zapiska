<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Verification Code</title>
</head>
<body>
    <h2>Hello {{ $user-> username }},</h2>
    <p>Thank you for registering! Your verification code is:</p>

    <h1 style="color: #2d89ef;">{{ $code }}</h1>

    <p>This code will expire in 3 minutes.</p>

    <p>Regards,<br>{{ config('app.name') }}</p>
</body>
</html>
