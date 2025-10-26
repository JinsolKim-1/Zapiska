<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Company Rejected</title>
</head>
<body>
    <div class="container">
        <h1>Company Rejected</h1>
        <p>Hello {{ $company->creator->firstname ?? 'User' }},</p>
        <p>Your company <strong>{{ $company->company_name }}</strong> has been <strong>rejected</strong> by the admin. Please review your details and try again if needed.</p>
        <p class="footer">Thanks,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
