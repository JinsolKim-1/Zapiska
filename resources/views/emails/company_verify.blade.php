<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verify Your Company</title>
</head>
<body>
    <div class="container">
        <h1>Verify Your Company</h1>
        Hello {{ $firstName ?? 'User' }}
        <p>You submitted a company registration for <strong>{{ $companyName }}</strong>. Please verify your company by clicking the button below:</p>
        <a class="btn" href="{{ $verifyUrl }}">Verify Company</a>
        <p>If you did not request this, please ignore this email.</p>
        <p>Thanks,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
