<?php

/**
 * @var \CodeIgniter\View\View $this
 * @var string $org_name
 * @var string $org_slug
 */

$this->extend('layouts/auth');

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
    <?= form_open(route_to('login', $org_slug), [
            'class' => 'space-y-6',
            'id'    => 'login-form'
    ]) ?>
    <div class="space-y-1.5">
        <?= view('components/form-input', [
                'props' => [
                        'id'               => 'email_or_phone',
                        'label'            => 'Email or Phone',
                        'name'             => 'email_or_phone',
                        'value'            => old('auth_type') === 'phone' ? old('phone') : old('email'),
                        'outer-class'      => 'basis-3/4 md:ml-0 md:mr-2',
                        'invalid-feedback' => old('auth_type') === 'phone'
                                ? validation_show_error('phone')
                                : validation_show_error('email'),
                        'has-label'        => true,
                        'required'         => true,
                ]
        ]) ?>
    </div>

    <div class="space-y-1.5">
        <div class="flex items-center justify-between">
            <label for="password" class="block text-sm font-medium leading-6 text-soko-500">Password</label>
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
                        'invalid-feedback' => validation_show_error('password'),
                        'has-label'        => false,
                        'required'         => true,
                ]
        ]) ?>
    </div>


    <input type="hidden" name="auth_type">

    <div>
        <button type="submit"
                class="btn btn-sm btn-block normal-case btn-primary text-white">
            Sign in
        </button>
    </div>
    <?= form_close() ?>

</div>

<?php $this->endSection(); ?>

<?php $this->section('bottom-scripts'); ?>
<script>
    $(document).ready(function () {
        $('#login-form').on('submit', function (e) {
            const authTypeInput = $('input[name="auth_type"]');
            const emailOrPhoneInput = $('#email_or_phone');

            // Check if the auth_type input already has a value
            if (authTypeInput.val().trim() === '') {
                e.preventDefault(); // Prevent form submission

                // Determine if the email_or_phone input is an email or phone number
                const emailOrPhoneValue = emailOrPhoneInput.val().trim();
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
                    emailOrPhoneInput.attr('name', 'email');
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
                        emailOrPhoneInput.attr('name', 'phone');
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
