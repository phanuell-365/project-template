<?php

$this->extend('layouts/auth');

$emailOrPhoneInputErrorFeedback = validation_show_error('email_or_phone');
$passwordInputErrorFeedback = validation_show_error('password');

$_errors = session()->getFlashdata('errors');

//dd('here', $_errors);
?>

<?php $this->section('title'); ?>
Login - <?= esc($org_name ?? 'Organization') ?>
<?php $this->endSection(); ?>

<?php $this->section('heading'); ?>
Sign in to your account
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<h1 class="sr-only">
    Welcome to Sokojumla's salesforce login. Click the link below to login to your account.
</h1>

<div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
    <form class="space-y-6" action="<?= route_to('login', $org_slug) ?>" method="POST" id="login-form">
        <div class="space-y-1.5">
            <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                Email or Phone
            </label>
            <?= view('components/form-input', [
                    'props' => [
                            'id'               => 'email_or_phone',
                            'label'            => 'Email or Phone',
                            'name'             => 'email_or_phone',
                            'value'            => old('email_or_phone'),
                            'outer-class'      => 'basis-3/4 md:ml-0 md:mr-2',
                            'invalid-feedback' => 'Please enter your email or phone number',
                            'has-label'        => false,
                            //                            'has-error'        => !empty($emailOrPhoneInputErrorFeedback) && !empty(old('email_or_phone')),
                            //                            'error-feedback'   => $emailOrPhoneInputErrorFeedback,
                            //                            'errors'           => [$errors['phone'] ?? $errors['email'] ?? []],
                            'errors'           => [
                                    'email_or_phone' => $_errors['phone'] ?? $errors['email'] ?? ''
                            ]
                    ]
            ]) ?>
        </div>

        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                <div class="text-sm">
                    <a href="<?= route_to('forgot-password-view', $org_slug) ?>"
                       class="font-semibold text-soko-600 hover:text-soko-500">Forgot password?</a>
                </div>
            </div>

            <?= view('components/form-input', [
                    'props' => [
                            'id'               => 'password',
                            'label'            => 'Password',
                            'name'             => 'password',
                            'type'             => 'password',
                            'value'            => old('password'),
                            'outer-class'      => 'basis-3/4 md:ml-0 md:mr-2',
                            'invalid-feedback' => 'Please enter your password',
                            'has-label'        => false,
                            //                            'has-error'        => !empty($passwordInputErrorFeedback) && !empty(old('password')),
                            //                            'error-feedback'   => $passwordInputErrorFeedback,
                            'errors'           => [
                                    'password' => $errors['password'] ?? ''
                            ]
                    ]
            ]) ?>
        </div>

        <input type="hidden" name="auth_type">

        <?= csrf_field('') ?>

        <div>
            <button type="submit"
                    class="btn btn-sm btn-block normal-case btn-primary text-white">
                Sign in
            </button>
        </div>
    </form>

</div>

<?php $this->endSection(); ?>

<?php $this->section('bottom-scripts'); ?>
<script>
    $(document).ready(function () {
        $('#login-form').on('submit', function (e) {
            const authTypeInput = $('input[name="auth_type"]');

            // Check if the auth_type input already has a value
            if (authTypeInput.val().trim() === '') {
                e.preventDefault(); // Prevent form submission

                // Determine if the email_or_phone input is an email or phone number
                const emailOrPhoneValue = $('#email_or_phone').val().trim();
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                // const phonePattern = /^\+?[0-9]{7,15}$/; // Simple phone number pattern
                const phonePatterns = [
                    // Local format starting with 07
                    /^07[0-9]{8}$/,
                    // International format starting with +2547
                    /^\+2547[0-9]{8}$/,
                    // International format starting with 2547
                    /^2547[0-9]{8}$/,
                    // Local landline format starting with 01
                    /^01[0-9]{8}$/,
                    // International landline format starting with +2541
                    /^\+2541[0-9]{8}$/,
                    // International landline format starting with 2541
                    /^2541[0-9]{8}$/
                ]

                if (emailPattern.test(emailOrPhoneValue)) {
                    authTypeInput.val('email');
                    // we'll need to change the name of the input to email
                    $('#email_or_phone').attr('name', 'email');
                    // } else if (phonePattern.test(emailOrPhoneValue)) {
                    //     authTypeInput.val('phone');
                    // } else {
                    // If neither, you can choose to set a default or show an error
                    // authTypeInput.val('unknown');
                    // }
                } else {
                    let isPhone = false;
                    for (const pattern of phonePatterns) {
                        if (pattern.test(emailOrPhoneValue)) {
                            isPhone = true;
                            break;
                        }
                    }
                    if (isPhone) {
                        authTypeInput.val('phone');

                        // we'll need to change the name of the input to phone
                        $('#email_or_phone').attr('name', 'phone');
                    } else {
                        // If neither, you can choose to set a default or show an error
                        authTypeInput.val('unknown');
                    }
                }

                // Now submit the form
                this.submit();
            }
        });
    });
</script>
<?php $this->endSection(); ?>
