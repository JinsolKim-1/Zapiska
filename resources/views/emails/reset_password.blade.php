<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Your Password</title>
</head>
<body>
    <h2>Password Reset Request</h2>
    <p>Hello {{ $user->username }},</p>
    <p>You requested to reset your password. Click the link below to set a new password:</p>
    <p><a href="{{ $resetLink }}">Reset Password</a></p>
    <p>This link will expire in 60 minutes.</p>
    <p>If you did not request a password reset, please ignore this email.</p>
    <br>
    <p>Thanks, <br> Your App Team</p>
</body>
</html>
