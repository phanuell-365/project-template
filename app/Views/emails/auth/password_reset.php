<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; background-color:#f1f5f9; padding:32px;">
    <tr>
        <td align="center">
            <table role="presentation" cellpadding="0" cellspacing="0" width="640" style="background-color:#ffffff; border-radius:10px; overflow:hidden;">
                <tr>
                    <td style="padding:28px; border-bottom:1px solid #e5e7eb;">
                        <h1 style="margin:0 0 8px; font-size:22px; color:#111827;">Reset your password</h1>
                        <p style="margin:0; color:#6b7280; font-size:15px;">A password reset request was received for your account.</p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:28px; color:#111827; font-size:15px; line-height:1.6;">
                        <p>Hi {{user_name}},</p>

                        <p>We received a request to reset the password associated with your account. To proceed, please use the link below to create a new password.</p>

                        <p><a href="{{reset_link}}" style="display:inline-block; padding:12px 18px; border-radius:6px; text-decoration:none; border:1px solid #ef4444;">Reset your password</a></p>

                        <p>This password reset link will remain valid for <strong>{{expiration_time}}</strong>.</p>

                        <p>If you did not request a password reset, no action is required. Your account remains secure.</p>

                        <p style="margin-top:28px;">Regards,<br />The {{app_name}} Team</p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:18px; text-align:center; font-size:12px; color:#9ca3af; border-top:1px solid #e5e7eb;">© {{app_name}}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
