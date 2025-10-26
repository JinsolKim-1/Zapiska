<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Company Approved</title>
</head>
<body>
    <div class="container">
        <h1>Company Approved</h1>
        <p>Hello {{ $company->creator->firstname ?? 'User' }},</p>
        <p>Your company <strong>{{ $company->company_name }}</strong> has been <strong>approved</strong> by the admin.</p>
        <p>Thanks,<br>{{ config('app.name') }}</p>
