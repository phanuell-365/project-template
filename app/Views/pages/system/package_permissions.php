<?php

$this->extend('layouts/main');
//dd($package_permissions, $all_permissions);


// We're going to create checkboxes for each permission, grouped by parent permissions
// The all_permissions array contains grouped permissions per parent
// And the package_permissions array contains all the permissions assigned to the package
$assigned_permission_ids = array_column($package_permissions, 'permission_id');

//dd($assigned_permission_ids);
?>

<?php $this->section('title'); ?>
Package permissions - <?= esc($org_name ?? 'Organization') ?>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<!--<h1 class="text-2xl font-semibold text-gray-900">-->
<!--    Package Permissions for --><?php //= esc($org_name ?? 'Organization') ?>
<!--</h1>-->

<h1 class="text-3xl font-bold text-gray-900 mb-2">
    Package Permission Management
</h1>
<p class="text-gray-600 text-sm">
    Configure which permissions are included in this package. Parent permissions automatically grant
    access to all child permissions.
</p>

<div class="max-w-7xl mx-auto py-6">
    <!-- Header Section -->
    <div class="bg-white shadow-sm rounded-lg py-6 mb-6">
        <div class="flex justify-between items-start">
            <div>

                <div class="mt-4 flex items-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-soko-100 text-soko-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd"
                                  d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Package ID: <?= esc($packageId) ?>
                    </span>
                    <span id="permission-counter"
                          class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                        <span id="selected-count"><?= count($assigned_permission_ids) ?></span> Selected
                    </span>
                </div>
            </div>
            <div class="flex flex-col space-y-2">
                <a href="<?= route_to('package-settings', $org_slug) ?>"
                   class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-soko-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Packages
                </a>
                <button type="button" id="select-all-btn"
                        class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-soko-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Select All
                </button>
            </div>
        </div>
    </div>

    <form action="<?= route_to('package-permissions-update', $org_slug) ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="package_id" value="<?= esc($packageId) ?>">

        <!-- Permissions Grid -->
        <div class="space-y-6">
            <?php foreach ($all_permissions as $index => $parent): ?>
                <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <!-- Parent Permission Header -->
                    <div class="bg-gradient-to-r from-soko-200/60 to-soko-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-soko-600 rounded-lg flex items-center justify-center">
                                        <?php if (!empty($parent['icon'])): ?>
                                            <!--                                            <i class="--><?php //= esc($parent['icon']) ?><!-- text-white text-lg"></i>-->
                                            <span class="material-symbols-rounded text-white">
                                            <?= esc($parent['icon']) ?>
                                        </span>
                                        <?php else: ?>
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <input id="parent_<?= esc($parent['permission_id']) ?>"
                                           name="permissions[]"
                                           value="<?= esc($parent['permission_id']) ?>"
                                           type="checkbox"
                                           class="parent-checkbox h-5 w-5 rounded border-gray-300 text-soko-600 focus:ring-soko-500 cursor-pointer"
                                           data-parent-id="<?= esc($parent['permission_id']) ?>"
                                            <?= in_array($parent['permission_id'], $assigned_permission_ids) ? 'checked' : '' ?>>
                                    <label for="parent_<?= esc($parent['permission_id']) ?>"
                                           class="ml-3 cursor-pointer">
                                        <span class="text-lg font-semibold text-gray-900"><?= esc($parent['name']) ?></span>
                                        <span class="text-sm text-gray-600 mt-1"><?= esc($parent['description']) ?></span>
                                    </label>
                                </div>
                            </div>
                            <?php if (!empty($parent['children'])): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-soko-100 text-soko-800">
                                    <?= count($parent['children']) ?> child permission<?= count($parent['children']) > 1 ? 's' : '' ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Child Permissions -->
                    <?php if (!empty($parent['children'])): ?>
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php foreach ($parent['children'] as $child): ?>
                                    <div class="relative flex items-start p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-soko-300 transition-all duration-150">
                                        <div class="flex items-center h-5">
                                            <input id="child_<?= esc($child['permission_id']) ?>"
                                                   name="permissions[]"
                                                   value="<?= esc($child['permission_id']) ?>"
                                                   type="checkbox"
                                                   class="child-checkbox h-4 w-4 rounded border-gray-300 text-soko-600 focus:ring-soko-500 cursor-pointer"
                                                   data-parent-id="<?= esc($parent['permission_id']) ?>"
                                                    <?= in_array($child['permission_id'], $assigned_permission_ids) ? 'checked' : '' ?>>
                                        </div>
                                        <div class="ml-3 text-sm flex-1">
                                            <label for="child_<?= esc($child['permission_id']) ?>"
                                                   class="font-medium text-gray-900 cursor-pointer"><?= esc($child['name']) ?></label>
                                            <p class="text-gray-500 text-xs mt-1"><?= esc($child['description']) ?></p>
                                            <?php if (!empty($child['context'])): ?>
                                                <!--                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 mt-2">-->
                                                <!--                                                    --><?php //= esc($child['context']) ?>
                                                <!--                                                </span>-->
                                                <?php if ($child['context'] === 'admin'): ?>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mt-2">
                                                        <?= esc($child['context']) ?>
                                                </span>
                                                <?php elseif ($child['context'] === 'app'): ?>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-2">
                                                        <?= esc($child['context']) ?>
                                                </span>
                                                <?php elseif ($child['context'] === 'both'): ?>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-2">
                                                        <?= esc($child['context']) ?>
                                                </span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Action Bar -->
        <div class="mt-8 bg-white shadow-sm rounded-lg p-6 sticky bottom-4 border border-gray-200">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    Make sure to save your changes before leaving this page.
                </div>
                <div class="flex space-x-3">
                    <button type="button" id="reset-btn"
                            class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-soko-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset Changes
                    </button>
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-md border border-transparent bg-soko-600 px-6 py-3 text-sm font-medium text-white shadow-sm hover:bg-soko-700 focus:outline-none focus:ring-2 focus:ring-soko-500 focus:ring-offset-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Permissions
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php $this->endSection(); ?>

<?php
$this->section('bottom-scripts');
?>

<script>
    $(document).ready(function () {
        const $parentCheckboxes = $('.parent-checkbox');
        const $childCheckboxes = $('.child-checkbox');
        const $selectAllBtn = $('#select-all-btn');
        const $resetBtn = $('#reset-btn');
        const $selectedCountElement = $('#selected-count');

        function updateCounter() {
            const totalChecked = $('input[type="checkbox"]:checked').length;
            $selectedCountElement.text(totalChecked);
        }

        $parentCheckboxes.on('change', function () {
            const parentId = $(this).data('parent-id');
            const isChecked = $(this).prop('checked');
            $(`.child-checkbox[data-parent-id="${parentId}"]`).prop('checked', isChecked);
            updateCounter();
        });

        $childCheckboxes.on('change', function () {
            const parentId = $(this).data('parent-id');
            const $parentCheckbox = $(`.parent-checkbox[data-parent-id="${parentId}"]`);
            const $allChildren = $(`.child-checkbox[data-parent-id="${parentId}"]`);
            const allChecked = $allChildren.length === $allChildren.filter(':checked').length;
            const noneChecked = $allChildren.filter(':checked').length === 0;

            if (allChecked) {
                $parentCheckbox.prop('checked', true).prop('indeterminate', false);
            } else if (noneChecked) {
                $parentCheckbox.prop('checked', false).prop('indeterminate', false);
            } else {
                $parentCheckbox.prop('indeterminate', true);
            }
            updateCounter();
        });

        $selectAllBtn.on('click', function () {
            const $allCheckboxes = $('input[type="checkbox"]');
            const allChecked = $allCheckboxes.length === $allCheckboxes.filter(':checked').length;
            $allCheckboxes.prop('checked', !allChecked);
            $(this).html(allChecked ?
                '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Select All' :
                '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Deselect All');
            updateCounter();
        });

        $resetBtn.on('click', function () {
            location.reload();
        });

        // Initial state setup
        $parentCheckboxes.each(function () {
            const parentId = $(this).data('parent-id');
            const $parentCheckbox = $(this);
            const $allChildren = $(`.child-checkbox[data-parent-id="${parentId}"]`);
            const totalChildren = $allChildren.length;
            const checkedChildren = $allChildren.filter(':checked').length;

            if (totalChildren > 0) {
                if (checkedChildren === totalChildren) {
                    $parentCheckbox.prop('checked', true);
                    $parentCheckbox.prop('indeterminate', false);
                } else if (checkedChildren > 0 && checkedChildren < totalChildren) {
                    $parentCheckbox.prop('indeterminate', true);
                } else {
                    $parentCheckbox.prop('indeterminate', false);
                }
            }
        });

        updateCounter();
    });
</script>

<?php $this->endSection(); ?>
