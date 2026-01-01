<?php

/**
 * @var array $log_details
 */

//dd($log_details);

$this->extend('layouts/main');
?>

<?php $this->section('content'); ?>

<h1 class="text-3xl font-bold text-gray-900 mb-2">
    View Communication Log Details
</h1>

<div class="grid grid-cols-1 gap-4">
    <div class="px-6 py-8 flex flex-col rounded-xl bg-white mb-4 md:mb-6">
        <div class="relative max-lg:flex-col flex gap-6">
            <!-- logo -->
            <div class="flex items-center justify-center border border-gray-100 rounded-lg overflow-hidden">
                <div class="w-40 h-40 rounded-full bg-gradient-to-r from-soko-500 to-soko-600 flex items-center justify-center text-white font-semibold text-7xl">
                    <?php // get the first characters of each word in the organization name
                    $org_name = $log_details['organization_name'] ?? 'Organization Name';
                    $initials = '';
                    foreach (explode(' ', $org_name) as $word) {
                        $initials .= strtoupper($word[0]);
                    }
                    echo substr($initials, 0, 2);
                    ?>
                </div>
            </div>
            <!-- info -->
            <div class="flex flex-col flex-grow gap-2">
                <div class="flex items-center justify-between pb-1.5">
                    <h2 class="text-title-lg font-bold flex items-center gap-2">
                        <?= esc($log_details['organization_name'] ?? 'Organization Name') ?>
                    </h2>

                    <!-- star -->
                    <div class="hover:text-orange-500 pe-2">
                        <input id="check_17" class="hidden peer toggle-star" type="checkbox" name="check_17">
                        <label for="check_17" class="peer-checked:text-orange-500 hover:cursor-pointer starfill">
                            <span class="material-symbols-rounded">star</span>
                        </label>
                    </div>
                </div>

                <!-- contact -->
                <div class="flex items-center justify-between w-full py-4 border-y border-gray-100">
                    <div class="flex flex-row items-center gap-4">
                        <a href="mailto:<?= esc($log_details['organization_email'] ?? 'N/A') ?>"
                           class="btn btn-primary">
                    <span class="flex flex-row items-center gap-1">
                      <span class="material-symbols-rounded">mail</span>
                      <span class="inline md:hidden">Email</span>
                    </span>
                            <span class="hidden md:inline text-body-md">
                                <?= esc($log_details['organization_email'] ?? 'N/A') ?>
                            </span>
                        </a>

                        <a href="tel:<?= esc($log_details['organization_phone'] ?? 'N/A') ?>"
                           class="btn btn-secondary">
                    <span class="flex flex-row items-center gap-1">
                      <span class="material-symbols-rounded">call</span>
                      <span class="inline md:hidden">Call</span>
                    </span>
                            <span class="hidden md:inline text-body-md">
                                <?= esc($log_details['organization_phone'] ?? 'N/A') ?>
                            </span>
                        </a>
                    </div>

                    <div class="relative">
                        <button type="button"
                                id="log-menu-button"
                                class="flex items-center space-x-2 p-2 text-gray-700 rounded-lg hover:bg-soko-50 transition-all duration-200">
                            <span class="material-symbols-rounded text-gray-400"
                                  style="font-size: 20px;">more_vert</span>
                        </button>

                        <!-- Dropdown menu -->
                        <div id="log-menu"
                             class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200/80 py-1 z-50">
                            <a href="<?= base_url('profile') ?>"
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-soko-50 hover:text-soko-700 transition-colors duration-150">
                                <span class="material-symbols-rounded text-gray-400 mr-3" style="font-size: 20px;">
                                    replay
                                </span>
                                Resend
                            </a>
                            <a href="<?= base_url('settings') ?>"
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-soko-50 hover:text-soko-700 transition-colors duration-150">
                                <span class="material-symbols-rounded text-gray-400 mr-3" style="font-size: 20px;">settings</span>
                                Settings
                            </a>
                            <hr class="my-1 border-gray-200">
                            <a href="<?= route_to('logout', $org_slug) ?>"
                               class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150">
                                <span class="material-symbols-rounded mr-3" style="font-size: 20px;">logout</span>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>

                <!-- short info -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 pt-3">
                    <div class="flex flex-col gap-2">
                        <span class="text-xs font-medium leading-4 tracking-wider text-gray-500">Channel</span>
                        <h4 class="text-[1rem] font-medium leading-5 tracking-tight mb-3">
                            <?= esc($log_details['channel'] ?? 'N/A') ?>
                        </h4>
                    </div>
                    <div class="flex flex-col gap-2">
                        <span class="text-xs font-medium leading-4 tracking-wider text-gray-500">Email</span>
                        <h4 class="text-[1rem] font-medium leading-5 tracking-tight mb-3">
                            <?= esc($log_details['recipient_email'] ?? 'N/A') ?>
                        </h4>
                    </div>
                    <div class="flex flex-col gap-2">
                        <span class="text-xs font-medium leading-4 tracking-wider text-gray-500">Phone</span>
                        <h4 class="text-[1rem] font-medium leading-5 tracking-tight mb-3">
                            <?= esc($log_details['recipient_phone'] ?? 'N/A') ?>
                        </h4>
                    </div>
                    <div class="flex flex-col gap-2">
                        <span class="text-xs font-medium leading-4 tracking-wider text-gray-500">Priority</span>
                        <h4 class="text-[1rem] font-medium leading-5 tracking-tight mb-3">
                            <?= esc($log_details['priority'] ?? 'N/A') ?>
                        </h4>
                    </div>
                    <div class="flex flex-col gap-2">
                        <span class="text-xs font-medium leading-4 tracking-wider text-gray-500">Sent At</span>
                        <h4 class="text-[1rem] font-medium leading-5 tracking-tight mb-3">
                            <?= esc($log_details['sent_at'] ?? 'N/A') ?>
                        </h4>
                    </div>
                    <div class="flex flex-col gap-2">
                        <span class="text-xs font-medium leading-4 tracking-wider text-gray-500">Status</span>
                        <h4 class="text-[1rem] font-medium leading-5 tracking-tight mb-3">
                            <?= esc($log_details['status'] ?? 'N/A') ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Render the message subject here before the body -->
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Subject</h3>
            <div class="p-4 border border-gray-300 rounded-lg shadow-sm bg-gray-50">
                <p class="text-gray-900"><?= esc($log_details['subject'] ?? 'No subject available.') ?></p>
            </div>
        </div>

        <!-- If the log is an email, then we'll render the subject here but within an iframe to simulate an email preview -->
        <?php if (isset($log_details['channel']) && strtolower($log_details['channel']) === 'email'): ?>
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Email Preview</h3>
                <iframe srcdoc="<?= esc($log_details['body'] ?? '<p>No content available.</p>') ?>"
                        class="w-full h-[100dvh] border border-gray-300 rounded-lg shadow-sm"
                        sandbox="allow-same-origin allow-scripts allow-popups allow-forms">
                </iframe>
            </div>
        <?php else: ?>
            <div class="mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Message Body</h3>
                <div class="p-4 border border-gray-300 rounded-lg shadow-sm bg-gray-50">
                    <p class="text-gray-900 whitespace-pre-wrap"><?= esc($log_details['body'] ?? 'No content available.') ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $this->endSection(); ?>

<?php
$this->section('bottom-scripts');
?>

<script>
    $(document).ready(function () {
        // Toggle dropdown menu
        $('#log-menu-button').on('click', function (e) {
            e.preventDefault();
            $('#log-menu').toggleClass('hidden');
        });

        // Close the dropdown if clicked outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#log-menu-button').length && !$(e.target).closest('#log-menu').length) {
                $('#log-menu').addClass('hidden');
            }
        });
    });
</script>

<?php $this->endSection(); ?>
