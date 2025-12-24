<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class SettingsSchema
{
    /**
     * The Master Definition of all configurable settings in the system.
     * We'll user Google Material Symbols for the icons.
     */
    public static array $structure = [
        'email'    => [
            'title' => 'Email Settings',
//            'icon'  => 'envelope',
            'icon'  => 'email',
            'order' => 3,
            'value' => [
                'smtp_host'       => [
                    'label'       => 'SMTP Host',
                    'helper-text' => 'The hostname of your SMTP server.',
                    'validation'  => 'required|string',
                    'type'        => 'text',
                    'default'     => 'localhost'
                ],
                'smtp_port'       => [
                    'label'       => 'SMTP Port',
                    'helper-text' => 'The port number for your SMTP server.',
                    'validation'  => 'required|numeric',
                    'type'        => 'number',
                    'default'     => 25
                ],
                'smtp_username'       => [
                    'label'       => 'SMTP Username',
                    'helper-text' => 'The username for SMTP authentication.',
                    'validation'  => 'required|string',
                    'type'        => 'email',
                    'default'     => ''
                ],
                'smtp_pass'       => [
                    'label'       => 'SMTP Password',
                    'helper-text' => 'The password for SMTP authentication.',
                    'validation'  => 'required|string',
                    'type'        => 'password',
                    'default'     => ''
                ],
                'from_address'    => [
                    'label'       => 'From Address',
                    'helper-text' => 'The email address that emails will be sent from.',
                    'validation'  => 'required|valid_email',
                    'type'        => 'email',
                    'default'     => ''
                ],
                'from_name'       => [
                    'label'       => 'From Name',
                    'helper-text' => 'The name that emails will be sent from.',
                    'validation'  => 'required|string',
                    'type'        => 'text',
                    'default'     => ''
                ],
                'smtp_encryption' => [
                    'label'       => 'SMTP Encryption',
                    'helper-text' => 'The encryption method for SMTP (e.g., ssl, tls).',
                    'validation'  => 'permit_empty|in_list[ssl,tls]',
                    'type'        => 'select',
                    'options'     => [
                        'ssl' => 'SSL',
                        'tls' => 'TLS',
                    ],
                    'default'     => ''
                ],
                'smtp_timeout'    => [
                    'label'       => 'SMTP Timeout',
                    'helper-text' => 'The timeout duration (in seconds) for SMTP connections.',
                    'validation'  => 'required|numeric',
                    'type'        => 'number',
                    'default'     => 30
                ],
                'mail_type'       => [
                    'label'       => 'Mail Type',
                    'helper-text' => 'The format of the email (e.g., html, text).',
                    'validation'  => 'required|in_list[html,text]',
                    'type'        => 'select',
                    'options'     => [
                        'html' => 'HTML',
                        'text' => 'Plain Text',
                    ],
                    'default'     => 'html'
                ],
                'charset'         => [
                    'label'       => 'Character Set',
                    'helper-text' => 'The character set for the email (e.g., UTF-8).',
                    'validation'  => 'required|string',
                    'type'        => 'text',
                    'default'     => 'UTF-8'
                ],
                'smtp_protocol'   => [
                    'label'       => 'SMTP Protocol',
                    'helper-text' => 'The protocol to use for SMTP (e.g., smtp, sendmail).',
                    'validation'  => 'required|in_list[smtp,sendmail]',
                    'type'        => 'select',
                    'options'     => [
                        'smtp'     => 'SMTP',
                        'sendmail' => 'Sendmail',
                    ],
                    'default'     => 'smtp',
                ]
            ],
        ],
        'sms'      => [
            'title' => 'SMS Settings',
//            'icon'  => 'sms',
            'icon'  => 'message',
            'order' => 4,
            'value' => [
                'api_key'     => [
                    'label'       => 'API Key',
                    'helper-text' => 'The API key for your SMS service provider.',
                    'validation'  => 'required|string',
                    'type'        => 'text',
                    'default'     => ''
                ],
                'partner_id'  => [
                    'label'       => 'Partner ID',
                    'helper-text' => 'The Partner ID for your SMS service provider.',
                    'validation'  => 'required|string',
                    'type'        => 'text',
                    'default'     => ''
                ],
                'shortcode'   => [
                    'label'       => 'Shortcode',
                    'helper-text' => 'The shortcode used for sending SMS messages.',
                    'validation'  => 'required|string',
                    'type'        => 'text',
                    'default'     => ''
                ],
                'pass_type'   => [
                    'label'       => 'Password Type',
                    'helper-text' => 'The type of password authentication used.',
                    'validation'  => 'required|in_list[plain,hashed]',
                    'type'        => 'select',
                    'options'     => [
                        'plain'  => 'Plain Text',
                        'hashed' => 'Hashed',
                    ],
                    'default'     => 'plain'
                ],
                'gateway_url' => [
                    'label'       => 'Gateway URL',
                    'helper-text' => 'The URL of the SMS gateway.',
                    'validation'  => 'required|valid_url',
                    'type'        => 'text',
                    'default'     => ''
                ],
                'timeout'     => [
                    'label'       => 'Timeout',
                    'helper-text' => 'The timeout duration (in seconds) for SMS gateway connections.',
                    'validation'  => 'required|numeric',
                    'type'        => 'number',
                    'default'     => 30
                ],
            ],
        ],
        'payment'  => [
            'title' => 'Payment Settings',
//            'icon'  => 'credit-card',
            'icon'  => 'payment',
            'order' => 5,
            'value' => [
                'gateway_url'     => [
                    'label'       => 'Payment Gateway URL',
                    'helper-text' => 'The URL of the payment gateway.',
                    'validation'  => 'required|valid_url',
                    'type'        => 'text',
                    'default'     => ''
                ],
                'consumer_key'    => [
                    'label'       => 'Consumer Key',
                    'helper-text' => 'The consumer key for the payment gateway.',
                    'validation'  => 'required|string',
                    'type'        => 'text',
                    'default'     => ''
                ],
                'consumer_secret' => [
                    'label'       => 'Consumer Secret',
                    'helper-text' => 'The consumer secret for the payment gateway.',
                    'validation'  => 'required|string',
                    'type'        => 'password',
                    'default'     => '',
                ],
                'shortcode'       => [
                    'label'       => 'Shortcode',
                    'helper-text' => 'The shortcode used for payment transactions.',
                    'validation'  => 'required|string',
                    'type'        => 'text',
                    'default'     => ''
                ],
                'passkey'         => [
                    'label'       => 'Passkey',
                    'helper-text' => 'The passkey for payment authentication.',
                    'validation'  => 'required|string',
                    'type'        => 'password',
                    'default'     => '',
                ],
                'timeout'         => [
                    'label'       => 'Timeout',
                    'helper-text' => 'The timeout duration (in seconds) for payment gateway connections.',
                    'validation'  => 'required|numeric',
                    'type'        => 'number',
                    'default'     => 30
                ],
            ],
        ],
        'session'  => [
            'title' => 'Session Settings',
//            'icon'  => 'session',
            'icon'  => 'schedule',
            'order' => 2,
            'value' => [
                'session_timeout'        => [
                    'label'       => 'Session Timeout',
                    'helper-text' => 'The duration (in minutes) before a user session expires due to inactivity.',
                    'validation'  => 'required|numeric',
                    'type'        => 'number',
                    'default'     => 60
                ],
                'remember_me_duration'   => [
                    'label'       => 'Remember Me Duration',
                    'helper-text' => 'The duration (in days) for the "Remember Me" functionality.',
                    'validation'  => 'required|numeric',
                    'type'        => 'number',
                    'default'     => 30
                ],
                'jwt_private_key'        => [
                    'label'       => 'JWT Private Key',
                    'helper-text' => 'The private key used for signing JSON Web Tokens (JWT).',
                    'validation'  => 'required|string',
                    'type'        => 'textarea',
                    'default'     => ''
                ],
                'jwt_public_key'         => [
                    'label'       => 'JWT Public Key',
                    'helper-text' => 'The public key used for verifying JSON Web Tokens (JWT).',
                    'validation'  => 'required|string',
                    'type'        => 'textarea',
                    'default'     => ''
                ],
                'session_driver'         => [
                    'label'       => 'Session Driver',
                    'helper-text' => 'The storage mechanism for sessions (e.g., file, database, redis).',
                    'validation'  => 'required|in_list[file,database,redis]',
                    'type'        => 'select',
                    'options'     => [
                        'file'     => 'File',
                        'database' => 'Database',
                        'redis'    => 'Redis',
                    ],
                    'default'     => 'file'
                ],
                'session_cookie_name'    => [
                    'label'       => 'Session Cookie Name',
                    'helper-text' => 'The name of the cookie used to store the session ID.',
                    'validation'  => 'required|string',
                    'type'        => 'text',
                    'default'     => 'ci_session'
                ],
                'reset_token_expiration' => [
                    'label'       => 'Password Reset Token Expiration',
                    'helper-text' => 'The duration (in minutes) before a password reset token expires.',
                    'validation'  => 'required|numeric',
                    'type'        => 'number',
                    'default'     => 60
                ],
                'login_attempt_window'   => [
                    'label'       => 'Login Attempt Window',
                    'helper-text' => 'The time window (in minutes) to track failed login attempts.',
                    'validation'  => 'required|numeric',
                    'type'        => 'number',
                    'default'     => 15
                ],
            ],
        ],
        'security' => [
            'title' => 'Security Settings',
//            'icon'  => 'shield-alt',
            'icon'  => 'security',
            'order' => 6,
            'value' => [
                'password_min_length'      => [
                    'label'       => 'Password Minimum Length',
                    'helper-text' => 'The minimum number of characters required for user passwords.',
                    'validation'  => 'required|numeric',
                    'type'        => 'number',
                    'default'     => 8
                ],
                'password_require_special' => [
                    'label'       => 'Require Special Characters in Passwords',
                    'helper-text' => 'Whether passwords must include at least one special character.',
                    'validation'  => 'required|in_list[0,1]',
                    'type'        => 'select',
                    'options'     => [
                        '1' => 'Yes',
                        '0' => 'No',
                    ],
                    'default'     => 1
                ],
                'max_login_attempts'       => [
                    'label'       => 'Maximum Login Attempts',
                    'helper-text' => 'The maximum number of failed login attempts before locking the account.',
                    'validation'  => 'required|numeric',
                    'type'        => 'number',
                    'default'     => 5
                ],
                'account_lock_duration'    => [
                    'label'       => 'Account Lock Duration',
                    'helper-text' => 'The duration (in minutes) that an account remains locked after exceeding maximum login attempts.',
                    'validation'  => 'required|numeric',
                    'type'        => 'number',
                    'default'     => 15
                ],
            ],
        ],
        'site'     => [
            'title' => 'Site Settings',
//            'icon'  => 'cog',
            'icon'  => 'settings',
            'order' => 1,
            'value' => [
                'site_name'        => [
                    'label'       => 'Site Name',
                    'helper-text' => 'The name of the website or application.',
                    'validation'  => 'required|string',
                    'type'        => 'text',
                    'default'     => 'Project Name'
                ],
//                'site_logo_url'    => [
//                    'label'       => 'Site Logo URL',
//                    'helper-text' => 'The URL of the site logo image.',
//                    'validation'  => 'permit_empty|valid_url',
//                    'type'        => 'text',
//                    'default'     => ''
//                ],
                // We'll make logo an uploadable asset
                'site_logo'      => [
                    'label'       => 'Site Logo',
                    'helper-text' => 'Upload the site logo image.',
                    'validation'  => 'is_image[site_logo]|max_size[site_logo,5120]|mime_in[site_logo,image/jpg,image/jpeg,image/png,image/gif]',
                    'accepted-mime-types' => 'image/jpg,image/jpeg,image/png,image/gif',
                    'size-limit' => 2048, // in KB
                    'type'        => 'file',
                    'default'     => ''
                ],
                'site_favicon'    => [
                    'label'       => 'Site Favicon',
                    'helper-text' => 'Upload the site favicon image.',
                    'validation'  => 'is_image[site_favicon]|max_size[site_favicon,1024]|mime_in[site_favicon,image/x-icon,image/png,image/gif]',
                    'accepted-mime-types' => 'image/x-icon,image/png,image/gif',
                    'size-limit' => 1024, // in KB
                    'type'        => 'file',
                    'default'     => ''
                ],
                'site_description' => [
                    'label'       => 'Site Description',
                    'helper-text' => 'A brief description of the website or application.',
                    'validation'  => 'permit_empty|string',
                    'type'        => 'textarea',
                    'default'     => ''
                ],
                'default_language' => [
                    'label'       => 'Default Language',
                    'helper-text' => 'The default language for the application interface.',
                    'validation'  => 'required|string',
                    'type'        => 'text',
                    'default'     => 'en'
                ],
                'timezone'         => [
                    'label'       => 'Timezone',
                    'helper-text' => 'The default timezone for the application.',
                    'validation'  => 'required|string',
                    'type'        => 'text',
                    'default'     => 'UTC'
                ],
            ],
        ],
        'support'  => [
            'title' => 'Support Settings',
//            'icon'  => 'life-ring',
            'icon'  => 'support',
            'order' => 7,
            'value' => [
                'support_email' => [
                    'label'       => 'Support Email',
                    'helper-text' => 'The email address for customer support.',
                    'validation'  => 'required|valid_email',
                    'type'        => 'email',
                    'default'     => '',
                ],
                'support_phone' => [
                    'label'       => 'Support Phone',
                    'helper-text' => 'The phone number for customer support.',
                    'validation'  => 'required|string',
                    'type'        => 'tel',
                    'default'     => '',
                ],
            ],
        ],
    ];
}