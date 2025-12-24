<?php

namespace App\Libraries;

class TemplateRegistry
{
    public static array $definitions = [
        'auth.new_company' => [
            'name' => 'New Company Registration',
            'description' => 'Template for new company registration emails.',
            'placeholders' => [
                'company_name' => 'Name of the registered company',
                'registration_date' => 'Date of registration',
                'app_name' => 'Name of the application',
                'support_email' => 'Support email address',
                'support_phone' => 'Support phone number',
            ],
            'email' => [
                'default_subject' => 'Welcome to Our Service, {{company_name}}!',
                // the body will be loaded from a view file since it's usually HTML
                'default_body_view' => 'emails/auth/new_company',
            ],
            'sms' => [
                'default_message' => 'Hello {{company_name}}, your registration on {{registration_date}} was successful!',
            ],
        ],
        'auth.new_user' => [
            'name' => 'New User Registration',
            'description' => 'Template for new user registration emails.',
            'placeholders' => [
                'user_name' => 'Name of the registered user',
                'user_email' => 'Email of the registered user',
                'login_link' => 'Link to login page',
                'company_name' => 'Name of the user\'s company',
                'user_password' => 'Password of the registered user',
                'registration_date' => 'Date of registration',
            ],
            'email' => [
                'default_subject' => 'Welcome to Our Service, {{user_name}}!',
                // the body will be loaded from a view file since it's usually HTML
                'default_body_view' => 'emails/auth/new_user',
            ],
            'sms' => [
                'default_message' => 'Hello {{user_name}}, your registration on {{registration_date}} was successful!',
            ],
        ],
        'auth.password_reset' => [
            'name' => 'Password Reset',
            'description' => 'Template for password reset emails.',
            'placeholders' => [
                'user_name' => 'Name of the user',
                'reset_link' => 'Link to reset password',
                'expiration_time' => 'Time until the reset link expires',
            ],
            'email' => [
                'default_subject' => 'Password Reset Request',
                // the body will be loaded from a view file since it's usually HTML
                'default_body_view' => 'emails/auth/password_reset',
            ],
            'sms' => [
                'default_message' => 'Hello {{user_name}}, use this link to reset your password: {{reset_link}}. It expires in {{expiration_time}}.',
            ],
        ],
    ];
}