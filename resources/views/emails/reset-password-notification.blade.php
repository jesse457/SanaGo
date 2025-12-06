<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SanaGo</title>
    <style>
        /* BASE STYLES */
        body { margin: 0; padding: 0; min-width: 100%; width: 100% !important; height: 100% !important; background-color: #f4f6f8; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        a { text-decoration: none; }
        img { -ms-interpolation-mode: bicubic; }

        /* RESPONSIVE */
        @media only screen and (max-width: 600px) {
            .email-container { width: 100% !important; padding: 20px 15px !important; }
            .header-logo { margin-bottom: 20px !important; }
        }
    </style>
</head>
<body style="background-color: #f4f6f8; color: #333333;">

    <!-- WRAPPER -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f6f8;">
        <tr>
            <td align="center" style="padding: 40px 0;">

                <!-- MAIN CONTAINER -->
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="email-container" style="width: 600px; margin: 0 auto;">

                    <!-- 1. HEADER LOGO -->
                    <tr>
                        <td align="center" style="padding-bottom: 25px;">
                            <a href="{{ url('/') }}" style="display: inline-block;">
                                <!-- LOGO LOGIC: Tries to load image, falls back to text if image breaks -->
                                <img src="{{ \Storage::disk('central_public')->url('images/logo.png') }}"
                                     alt="SanaGo"
                                     height="32"
                                     style="height: 32px; width: auto; border: 0; display: block; font-family: sans-serif; font-size: 24px; font-weight: bold; color: #4F46E5;">
                            </a>
                        </td>
                    </tr>

                    <!-- 2. MAIN CARD -->
                    <tr>
                        <td bgcolor="#ffffff" style="border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">

                            <!-- Brand Accent Bar (Top of Card) -->
                            <div style="height: 4px; width: 100%; background-color: #4F46E5;"></div>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="padding: 40px 40px;">

                                        <!-- Headline -->
                                        <h1 style="margin: 0 0 20px; font-size: 22px; font-weight: 700; color: #1F2937; line-height: 1.3;">
                                            Reset your password
                                        </h1>

                                        <!-- Greeting & Context -->
                                        <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6; color: #4B5563;">
                                            Hello {{ $user->name }},
                                        </p>
                                        <p style="margin: 0 0 24px; font-size: 15px; line-height: 1.6; color: #4B5563;">
                                            We received a request to reset the password for your <strong>SanaGo</strong> account. If you made this request, click the button below. If you did not make this request, please ignore this email.
                                        </p>

                                        <!-- Call to Action Button -->
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td align="left" style="padding-bottom: 24px;">
                                                    <a href="{{ $url }}" style="display: inline-block; padding: 12px 28px; background-color: #4F46E5; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none; border-radius: 6px; -webkit-font-smoothing: antialiased;">
                                                        Reset Password
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Security Note -->
                                        <p style="margin: 0; font-size: 14px; line-height: 1.5; color: #6B7280; background-color: #F9FAFB; padding: 15px; border-radius: 6px; border: 1px solid #E5E7EB;">
                                            <span style="display: block; font-weight: 600; color: #374151; margin-bottom: 2px;">Security Notice:</span>
                                            This link is valid for 60 minutes. After that, you'll need to request a new reset link.
                                        </p>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 3. FOOTER (Professional/Legal) -->
                    <tr>
                        <td style="padding: 30px 0; text-align: center;">

                            <!-- Help Text -->
                            <p style="margin: 0 0 20px; font-size: 13px; color: #9CA3AF; line-height: 1.5; max-width: 400px; margin-left: auto; margin-right: auto;">
                                Button not working? Copy and paste this URL into your browser:<br>
                                <a href="{{ $url }}" style="color: #4F46E5; text-decoration: underline;">{{ $url }}</a>
                            </p>

                            <!-- Copyright & Address -->
                            <p style="margin: 0; font-size: 12px; color: #9CA3AF;">
                                &copy; {{ date('Y') }} SanaGo. All rights reserved.
                            </p>
                            <p style="margin: 5px 0 0; font-size: 12px; color: #9CA3AF;">
                                <!-- Optional Address Line -->
                                123 Business Street, Tech City, TC 90210
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- END MAIN CONTAINER -->
            </td>
        </tr>
    </table>
</body>
</html>
