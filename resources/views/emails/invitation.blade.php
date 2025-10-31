<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invitation</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 30px;">
    <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto;">
        <h2 style="color: #2c3e50;">You’ve been invited to Zapiska!</h2>
        <p>{{ $inviter_email }} has invited you to join Zapiska.</p>

        <p><strong>Invite UUID:</strong> {{ $invitation->invite_token }}</p>

        <p>Please click the button below to accept your invitation:</p>
        <p style="text-align: center;">
            <a href="{{ $invite_link }}" 
               style="display: inline-block; padding: 10px 20px; color: #fff; background-color: #3498db; border-radius: 5px; text-decoration: none;">
               Accept Invitation
            </a>
        </p>
        <p style="color: #777;">This invitation link will expire in 7 days.</p>
    </div>
</body>
</html>
