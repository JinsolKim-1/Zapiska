<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Edit Confirmation</title>
</head>
<body>

    <div class="email-container">
        <div class="email-header">
            <h1>Account Edit Confirmation</h1>
        </div>

        <div class="email-body">
            <p>Hello <strong>{{ $user->first_name ?? $user->super_username }}</strong>,</p>

            <p>
                Superadmin <strong>{{ $editor->super_username }}</strong> has requested to update your account details.
            </p>

            <p>
                Please confirm this change by clicking the button below:
            </p>

            <p>
                <a href="{{ $url }}" class="button">Confirm My Account Update</a>
            </p>

            <p>
                This link will expire in <strong>10 minutes</strong> for your security.<br>
                If you did not request this change, please ignore this email.
            </p>
        </div>

        <div class="email-footer">
            <p>Thanks,<br><strong>Zapiska Superadmin Team</strong></p>
        </div>
    </div>

</body>
</html>
