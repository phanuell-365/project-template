<?php
/**
 * @var string $content_body
 * @var $org_slug string
 */

$settings_service = service('settings_service');
$site_logo = $settings_service->getSiteLogoUrl($org_slug) ?? base_url('img/app-logo.png');
$support_address = $settings_service->getSetting($org_slug, 'support_address') ?? '1234 Default St, City, Country';
$support_email = $settings_service->getSetting($org_slug, 'support_email') ?? 'admin@acmecorp.com';
$support_phone = $settings_service->getSetting($org_slug, 'support_phone') ?? '+1 (555) 123-4567';
$app_name = $settings_service->getSiteName($org_slug) ?? 'AcmeCorp';
?>

<!doctype html>
<html lang="en" class="font-montserrat bg-light h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        <?= file_get_contents(FCPATH . 'css/email-output.css') ?>
    </style>
    <title>{{subject}}</title>
</head>
<body class="bg-soko-50 h-screen font-montserrat">
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" class="w-full p-8 bg-slate-100">
    <tr>
        <td align="center">
            <table role="presentation" cellpadding="0" cellspacing="0"
                   class="mx-auto w-full sm:max-w-xl lg:max-w-2xl bg-white rounded-lg overflow-hidden my-8 shadow-md">
                <tr>
                    <td
                            class="bg-soko-500 text-white text-center p-6 font-semibold text-lg border-b-4 border-soko-700">
                        <h1 class="text-2xl text-white font-bold m-0">
                            {{subject}}
                        </h1>

                        <p class="text-white/90 font-medium text-sm m-0">
                            {{description}}
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="color:#111827; line-height:1.6;" class="px-4 bg-white">
                        <div class="p-7 text-gray-900 leading-relaxed email-prose">
                            <?= $content_body ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:center; color:#9ca3af; border-top:1px solid #e5e7eb;" class="pt-4 px-4 bg-slate-50">
                        <img src="<?= $site_logo ?>" alt="app-logo" class="mx-auto h-10 w-auto my-4"/>
                        <address class="text-gray-500 text-xxxs">
                            <?= $support_address?><br>
                            <a href="mailto:<?= $support_email ?>" class="text-gray-500 underline"><?= $support_email ?></a><br>
                            <?= $support_phone ?>
                        </address>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:center; color:#9ca3af;" class="border-gray-200 px-4 bg-slate-50">
                        <p class="text-gray-500 text-xxxs px-4 mt-2">
                            Please do not reply to this email. This inbox is not monitored and you will not receive a
                            response.
                        </p>
                        <p class="text-gray-400 text-xxxs px-4 mt-2">
                            This email and any attachments are confidential and intended solely for the use of the
                            individual or entity to whom they are addressed. If you have received this email in error,
                            please notify the sender immediately and delete it from your system.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="text-align:center; color:#9ca3af;" class="border-gray-200 text-xxxs mt-5 pb-4 bg-slate-50">
                        © <?= date('Y') . ' ' . $app_name ?>. All rights reserved.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
