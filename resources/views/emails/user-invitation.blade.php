
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 40px 20px; }
        .card { background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .header { background-color: #4f46e5; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 40px; color: #374151; line-height: 1.6; }
        .btn { display: inline-block; background-color: #4f46e5; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 6px; font-weight: 600; margin-top: 20px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #9ca3af; }
        .warning { background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; font-size: 14px; color: #92400e; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <!-- Header -->
            <div class="header">
                <h1>Welcome to {{ $tenantName }}</h1>
            </div>

            <!-- Content -->
            <div class="content">
                <h2 style="margin-top:0;">Hello, {{ $user->name }}! 👋</h2>
                <p>
                    An administrator has created an account for you at <strong>{{ $tenantName }}</strong>.
                </p>
                <p>
                    To activate your account and access the portal, please click the button below to set your secure password.
                </p>

                <div style="text-align: center;">
                    <a href="{{ $resetUrl }}" class="btn">Set My Password</a>
                </div>

                <div class="warning">
                    <strong>Note:</strong> This invitation link will expire in 60 minutes for security reasons.
                </div>

                <p style="font-size: 14px; margin-top: 30px; color: #6b7280;">
                    If the button doesn't work, copy this link into your browser:<br>
                    <a href="{{ $resetUrl }}" style="color: #4f46e5; word-break: break-all;">{{ $resetUrl }}</a>
                </p>
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ $tenantName }}. All rights reserved.
        </div>
    </div>
</body>
</html>
