<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Zapiska Invitation</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 30px;">
    <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto;">
        <h2 style="color: #2c3e50;">You’ve been invited to Zapiska!</h2>

        <p>Hello <strong>{{ $invitee_email }}</strong>,</p>

        <p>
            You’ve been invited to join <strong>{{ $company_name }}</strong> on Zapiska — 
            a trusted collaboration and management platform.
        </p>

        <p><strong>Invitation Token:</strong> {{ $invitation->invite_token }}</p>

        <p>Please click the button below to accept your invitation:</p>

        <p style="text-align: center;">
            <a href="{{ $invite_link }}" 
               style="display: inline-block; padding: 12px 25px; color: #fff; background-color: #3498db; border-radius: 6px; text-decoration: none; font-weight: bold;">
               Accept Invitation
            </a>
        </p>

        <p style="color: #777; font-size: 14px;">
            This invitation link will expire in <b>3 days</b>.
        </p>

        <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">

        <p style="font-size: 13px; color: #555;">
            💡 To confirm this invitation is legitimate, you may contact 
            <strong>{{ $company_name }}</strong> directly at 
            <a href="mailto:{{ $company->company_email ?? 'support@zapiska.com' }}" style="color: #3498db; text-decoration: none;">
                {{ $company_email }}.
            </a>.
        </p>

        <p style="font-size: 12px; color: #888;">
            If you weren’t expecting this invitation, you can safely ignore this email.
        </p>
    </div>
</body>
</html>
