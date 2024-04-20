<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>
<h1>Password Reset Code</h1>
<p>Hello, {{ $user->name }}</p>
<p>You have requested a password reset. Your password reset code is:</p>
<h2>{{ $resetToken }}</h2>
<p>Please enter this code to complete your password reset process.</p>
<p>Thank you,</p>
</body>
</html>
