@php
    $brandColor = '#e04f6d';
    $buttonUrl = $url ?? url(route('password.reset', ['token' => $token, 'email' => $email ?? ''], false));
    $logoUrl = asset('assets/icons/WebPlan_logo.webp');
@endphp
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Reset your WedPlan password</title>
</head>
<body style="font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background:#faf9fa; color:#5a3f47; margin:0; padding:0; line-height:1.6;">
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#faf9fa;">
    <tr>
      <td align="center" style="padding:32px 16px;">
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:600px; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 8px 24px rgba(224,79,109,0.08); border:1px solid #f3e9ec;">
          <!-- Header with background -->
          <tr>
            <td style="background:linear-gradient(135deg, #fff 0%, #faf9fa 100%); padding:40px 36px 24px; text-align:center; border-bottom:1px solid #f3e9ec;">
              <img src="{{ $logoUrl }}" alt="WedPlan" width="120" height="auto" style="display:block; margin:0 auto 4px; max-height:80px;">
            </td>
          </tr>

          <!-- Main content -->
          <tr>
            <td style="padding:40px 36px;">
              <h1 style="font-family: 'Playfair Display', Georgia, serif; font-size:28px; font-weight:600; margin:0 0 12px; color:#3b2b33; text-align:center; line-height:1.3;">
                Reset Your Password
              </h1>

              <p style="margin:0 0 24px; color:#6b4a52; font-size:15px; text-align:center;">
                We received a request to reset the password for your WedPlan account. If you didn't make this request, you can safely ignore this email.
              </p>

              <!-- CTA Button -->
              <div style="text-align:center; margin:32px 0;">
                <a href="{{ $buttonUrl }}" style="display:inline-block; padding:14px 32px; background:{{ $brandColor }}; color:#fff; text-decoration:none; border-radius:12px; font-weight:600; font-size:16px; letter-spacing:0.3px; transition:background 0.2s ease; box-shadow:0 6px 18px rgba(224,79,109,0.2);">
                  Reset Password
                </a>
              </div>

              <p style="color:#6b4a52; margin:0 0 12px; font-size:13px; text-align:center;">
                This link will expire in {{ config('auth.passwords.users.expire') ?? 60 }} minutes for your security.
              </p>

              <!-- Security note -->
              <div style="background:#f9f0f2; border-left:4px solid {{ $brandColor }}; padding:12px 14px; margin:24px 0; border-radius:4px;">
                <p style="margin:0; font-size:12px; color:#6b4a52;">
                  <strong>Tip:</strong> Never share your password reset link with others. WedPlan support will never ask for your password.
                </p>
              </div>

              <!-- Help text -->
              <p style="color:#8b6a72; margin:20px 0 0; font-size:13px; text-align:center;">
                Need help? Reply to this email or contact our support team.
              </p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background:#faf9fa; padding:24px 36px; border-top:1px solid #f3e9ec; text-align:center;">
              <p style="margin:0 0 8px; font-size:12px; color:#9b7f86;">
                Happy planning! 💕
              </p>
              <p style="margin:0; font-size:12px; color:#9b7f86;">
                The WedPlan Team
              </p>
            </td>
          </tr>
        </table>

        <!-- Footer note -->
        <p style="font-size:11px; color:#a89497; margin:20px 0 0; text-align:center;">
          © {{ now()->year }} WedPlan. All rights reserved.
        </p>
      </td>
    </tr>
  </table>
</body>
</html>
