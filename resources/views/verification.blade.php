<!DOCTYPE html>
<html>
<head>
    <title>Verification Email</title>
</head>
<body>
<h1>Verification Code</h1>
<p>Hello, {{ $user->name }}</p>
<p>Thank you for registering. Your verification code is:</p>
<h2>{{ $verificationCode }}</h2>
<p>Please enter this code to complete your registration.</p>
<p>Thank you,</p>
</body>
</html>
