<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="font-family: Arial, Helvetica, sans-serif; background-color:#f8fafc; padding:32px;">
    <tr>
        <td align="center">
            <table role="presentation" cellpadding="0" cellspacing="0" width="640" style="background-color:#ffffff; border-radius:10px; overflow:hidden;">
                <tr>
                    <td style="padding:28px; border-bottom:1px solid #e5e7eb;">
                        <h1 style="margin:0 0 8px; font-size:22px; color:#111827;">Welcome to {{app_name}}</h1>
                        <p style="margin:0; color:#6b7280; font-size:15px;">Your user account has been created successfully.</p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:28px; color:#111827; font-size:15px; line-height:1.6;">
                        <p>Hi {{user_name}},</p>

                        <p>An account has been created for you at <strong>{{company_name}}</strong> on <strong>{{registration_date}}</strong>.</p>

                        <p>You can sign in using the following details:</p>

                        <ul style="padding-left:18px;">
                            <li><strong>Email:</strong> {{user_email}}</li>
                            <li><strong>Temporary password:</strong> {{user_password}}</li>
                        </ul>

                        <p>To access the system, please use the link below:</p>

                        <p><a href="{{login_link}}" style="display:inline-block; padding:12px 18px; border-radius:6px; text-decoration:none; border:1px solid #2563eb;">Log in to {{app_name}}</a></p>

                        <p><strong>Important:</strong> For security reasons, please change your password immediately after your first login.</p>

                        <p>If you experience any issues accessing your account, contact your system administrator or support team.</p>

                        <p style="margin-top:28px;">Welcome aboard,<br />The {{app_name}} Team</p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:18px; text-align:center; font-size:12px; color:#9ca3af; border-top:1px solid #e5e7eb;">© {{app_name}}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
