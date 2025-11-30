<?php

$this->extend('layouts/auth');

$emailInputErrorFeedback = validation_show_error('email');
$phoneInputErrorFeedback = validation_show_error('phone');
?>

<?php $this->section('title'); ?>
Forgot Password - <?= esc($org_name ?? 'Organization') ?>
<?php $this->endSection(); ?>

<?php $this->section('heading'); ?>
Forgot your password?
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form class="space-y-6" action="<?= route_to('forgot-password', $org_slug) ?>" method="POST"
              id="forgot-password-form">
            <div class="space-y-6 flex flex-col">
                <span class="text-gray-700 font-semibold">
                    Enter your email or phone number
                </span>
                <div class="flex items-center space-x-4">
                    <label>
                        <input class="text-primary" type="radio" name="reset_type" value="email" checked>
                        <span class="ml-2">Email</span>
                    </label>
                    <label>
                        <input class="text-primary" type="radio" name="reset_type" value="phone">
                        <span class="ml-2">Phone</span>
                    </label>
                    <input type="hidden" name="auth_type">
                </div>
            </div>
            <div class="space-y-1.5" id="email-con">
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                    Email
                </label>

                <?= view('components/form-input', [
                        'props' => [
                                'id'               => 'email',
                                'label'            => 'Email',
                                'name'             => 'email',
                                'value'            => old('email'),
                                'outer-class'      => '',
                                'invalid-feedback' => 'Please enter a valid email',
                                'has-label'        => false,
                                'has-error'        => !empty($emailInputErrorFeedback) && !empty(old('email')),
                                'error-feedback'   => $emailInputErrorFeedback,
                        ]
                ]) ?>
            </div>

            <div class="space-y-1.5 hidden" id="phone-con">
                <label for="phone" class="block text-sm font-medium leading-6 text-gray-900">
                    Phone
                </label>

                <?= view('components/form-input', [
                        'props' => [
                                'id'               => 'phone',
                                'label'            => 'Phone',
                                'name'             => 'phone',
                                'value'            => old('phone'),
                                'outer-class'      => '',
                                'invalid-feedback' => 'Please enter a valid phone number',
                                'has-label'        => false,
                                'has-error'        => !empty($phoneInputErrorFeedback) && !empty(old('phone')),
                                'error-feedback'   => $phoneInputErrorFeedback,
                        ]
                ]) ?>
            </div>

            <div>
                <button type="submit"
                        class="btn btn-sm btn-block normal-case btn-primary text-white">
                    Send Password Reset Link
                </button>
            </div>


            <p class="mt-10 text-center text-sm text-gray-500">
                Go back to
                <a href="<?= route_to('login-view', $org_slug) ?>"
                   class="font-semibold leading-6 text-soko-600 hover:text-soko-500">
                    Login
                </a>
            </p>
        </form>
    </div>
</div>

<?php $this->endSection(); ?>
