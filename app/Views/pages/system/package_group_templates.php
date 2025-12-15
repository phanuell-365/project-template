<?php
//$this->extend('layouts/main');
//dd($all_permissions);
?>
    <!---->
<?php                                      //$this->section('title'); ?>
    <!--Package Group Templates - --><?php //= esc($org_name ?? 'Organization') ?>
<?php                                      //$this->endSection(); ?>
    <!---->
<?php //$this->section('content'); ?>
    <!---->
    <!--<h1 class="text-2xl font-semibold text-gray-900">-->
    <!--    Package Permissions for --><?php //= esc($org_name ?? 'Organization') ?>
    <!--</h1>-->
    <!---->
    <!--<h1 class="text-3xl font-bold text-gray-900 mb-2">-->
    <!--    Package Group Templates-->
    <!--</h1>-->
    <!--<p class="text-gray-600 text-sm">-->
    <!--    Configure the permission templates for package groups within your organization.-->
    <!--</p>-->
    <!---->
    <!--<div class="max-w-screen-2xl mx-auto py-6">-->
    <!--    --><?php //= $createTabs($set_action) ?>
    <!--    --><?php //= package_permissions_settings_tabs('package_group_templates', $org_slug, $packageId) ?>
    <!---->
    <!--    <div class="border-b border-x border-gray-200 rounded-b-lg p-4">-->
    <!--        -->
    <!--    </div>-->
    <!--</div>-->
    <!---->
<?php //$this->endSection(); ?>

<?php
/**
 * @var array $group_templates
 * @var int $packageId
 * @var array $group_template_map
 * @var array $all_permissions
 * @var string $org_slug
 * @var string $org_name
 * @var string $groupId
 * @var array $assigned_permission_ids
 * @var array $group_template
 */
$this->extend('layouts/main');


//dd('groupid', $groupId, $group_template_map);

$form_textarea_controls = [
        'description' => [
                'id'          => 'description',
                'label'       => 'Description',
                'name'        => 'description',
                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
                'required'    => false,
                'placeholder' => 'A brief description of the group template',
                'rows'        => 4,
                'cols'        => 50,
                'value'       => !empty($group_template['description']) ? $group_template['description'] : '',
                'helper-text' => 'A brief description of the group template.',
        ]
];

$form_input_controls = [
        'name' => [
                'id'          => 'name',
                'label'       => 'Name',
                'name'        => 'name',
                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
                'type'        => 'text',
                'required'    => true,
                'placeholder' => 'Standard Package',
                'helper-text' => 'A descriptive name for the group template.',
                'value'       => !empty($group_template['name']) ? $group_template['name'] : '',
        ],
];
?>

<?php $this->section('title'); ?>
    Package Group Templates - <?= esc($org_name ?? 'Organization') ?>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

    <h1 class="text-3xl font-bold text-gray-900 mb-2">
        Package Group Templates
    </h1>
    <p class="text-gray-600 text-sm">
        Configure the permission templates for package groups within your organization.
    </p>

    <div class="grid grid-cols-1 py-6">
        <?= package_permissions_settings_tabs('package_group_templates', $org_slug, $packageId, ['group_id' => $groupId]); ?>

        <div class="border-b border-x border-gray-200 rounded-b-lg p-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Template Selector -->
                <div class="col-span-3">
                    <!--                    <label for="template-selector" class="block text-sm font-medium text-gray-700 mb-2">Select-->
                    <!--                        Template</label>-->
                    <!--                    <select id="template-selector"-->
                    <!--                            class="w-full md:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-soko-500 focus:ring-soko-500">-->
                    <!--                        --><?php //foreach ($group_template_map as $id => $name): ?>
                    <!--                            <option value="--><?php //= esc($id) ?><!--">-->
                    <?php //= esc($name) ?><!--</option>-->
                    <!--                        --><?php //endforeach; ?>
                    <!--                    </select>-->

                    <!-- Instead of rendering them as select options, we could render them as a list of buttons or links -->
                    <div class="space-y-2 border border-gray-200 rounded-lg max-h-96 overflow-y-auto flex flex-col bg-white shadow-2xs">
                        <div class="bg-gray-100 border-b border-gray-200 rounded-t-lg py-2 px-4 md:py-4 md:px-5">
                            <p class="mt-1 text-sm font-medium text-gray-500">
                                Available Group Templates
                            </p>
                        </div>
                        <div class="p-1.5 md:p-2 space-y-1.5 flex flex-col">
                            <?php foreach ($group_template_map as $id => $name): ?>
                                <?php // convert $groupId to int if it's not equal to 'new' ?>
                                <?php $groupId = $groupId !== 'new' ? (int)$groupId : $groupId; ?>
                                <a href="<?= $id === $groupId ? '#' : route_to('package-group-templates', $org_slug) . '?package_id=' . $packageId . '&group_id=' . $id ?>"
                                   data-template-id="<?= esc($id) ?>"
                                   class="w-full font-semibold text-sm px-4 py-2 rounded-md template-select-btn flex flex-row items-center justify-start active:scale-95 transition-all duration-150 gap-x-2
                                   <?= $id === $groupId ? 'bg-soko-100 text-soko-800 hover:bg-soko-200' :
                                           'bg-gray-50 hover:bg-gray-100 text-gray-800' ?>">
                                    <?php if ($id === 'new') : ?>
                                        <span class="material-symbols-rounded" aria-hidden="true">add</span>
                                    <?php else: ?>
                                        <span class="material-symbols-rounded" aria-hidden="true">edit</span>
                                    <?php endif; ?>
                                    <?= esc($name) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="col-span-9">
                    <!-- Template Form -->
                    <form id="template-form" method="post"
                          action="<?= route_to('create-package-group-template', $org_slug) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="package_id" value="<?= esc($packageId) ?>">
                        <input type="hidden" id="template_id" name="template_id" value="<?= esc($groupId) ?>">

                        <div class="grid grid-cols-1 gap-y-6">
                            <!-- Left Column: Template Details -->
                            <div class="lg:col-span-1">
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 rounded-t-lg">
                                        <h5 class="text-lg font-medium text-gray-900">Template Details</h5>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4 p-4">

                                        <?php foreach ($form_input_controls as $input_control): ?>
                                            <?= view('components/form-input', ['props' => $input_control]) ?>
                                        <?php endforeach; ?>

                                        <?php foreach ($form_textarea_controls as $textarea_control): ?>
                                            <?= view('components/form-textarea', ['props' => $textarea_control]) ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Permissions -->
                            <div class="lg:col-span-2 space-y-6">

                                <!-- Right Column: Permissions -->
                                <div class="lg:col-span-2 space-y-6">
                                    <?= view('partials/permission_grid', [
                                            'all_permissions'         => $all_permissions,
                                            'assigned_permission_ids' => $assigned_permission_ids
                                    ]) ?>
                                </div>

                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-6 flex justify-between items-center">
                            <?php // only show this button for existing templates ?>
                            <?php if ($groupId !== 'new') : ?>
                                <button type="button"
                                        id="delete-template"
                                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors text-xs font-bold inline-flex items-center">
                                    <!--                                <i class="fas fa-trash mr-2"></i> Delete Template-->
                                    <span class="material-symbols-rounded mr-2">delete</span> Delete Template
                                </button>
                            <?php endif; ?>
                            <div class="ml-auto space-x-3 flex items-center">
                                <button type="button"
                                        id="reset-btn"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors text-xs font-bold inline-flex items-center">
                                    <span class="material-symbols-rounded mr-2">restart_alt</span>
                                    Reset
                                </button>
                                <a href="<?= route_to('package-settings', $org_slug) ?>"
                                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors text-xs font-bold inline-flex items-center">
                                    <span class="material-symbols-rounded mr-2">cancel</span>
                                    Cancel
                                </a>
                                <button type="submit"
                                        id="save-template-btn"
                                        class="px-4 py-2 bg-soko-600 text-white rounded-md hover:bg-soko-700 transition-colors text-xs font-bold inline-flex items-center">
                                    <!--                                    <i class="fas fa-save mr-2"></i>-->
                                    <span class="material-symbols-rounded mr-2">save</span>
                                    <span id="save-btn-text">
<!--                                        Create Template-->
                                        <?= $groupId === 'new' ? 'Create Template' : 'Save Changes' ?>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script id="templates-data" type="application/json"><?= json_encode($group_templates) ?></script>
<?php $this->endSection(); ?>

<?php $this->section('bottom-scripts'); ?>
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

            // We have to submit this form as AJAX to handle errors properly and not to reset user's progress in case of error

            $('#template-form').on('submit', function (e) {
                e.preventDefault();
                const $form = $(this);
                const url = $form.attr('action');
                const formData = $form.serialize();

                //formData['<?php //= csrf_token() ?>//'] = '<?php //= csrf_hash() ?>//';

                // Get the permissions array and convert to JSON string
                const selectedPermissions = [];
                $form.find('input[name="permissions[]"]:checked').each(function () {
                    selectedPermissions.push($(this).val());
                });

                formData['permissions'] = JSON.stringify(selectedPermissions);

                $.post(url, formData)
                    .done(function (response, textStatus, xhr) {
                        console.debug('response', response);
                        // Handle success (e.g., show a success message, redirect, etc.)
                        // window.location.href = response.redirect_url;

                        // Get the new CSRF token from the response headers
                        const newCsrfToken = xhr.getResponseHeader('X-CSRF-Token');
                        if (newCsrfToken) {
                            // Update the CSRF token in the form
                            $form.find(`input[name='<?= csrf_token() ?>']`).val(newCsrfToken);
                        }

                        ShowNotification({
                            title: 'Success',
                            text: 'Package updated successfully.',
                            icon: 'success',
                        }).then(() => {
                            // Optionally, reload the page or update the package list
                            location.reload();
                        });
                    })
                    .fail(function (xhr) {
                        // console.error('Error:', xhr);
                        // Get the new CSRF token from the response
                        const newCsrfToken = xhr.getResponseHeader('X-CSRF-Token');
                        if (newCsrfToken) {
                            // Update the CSRF token in the form
                            $form.find(`input[name='<?= csrf_token() ?>']`).val(newCsrfToken);
                        }
                        // Handle errors (e.g., display error messages)
                        const errors = xhr.responseJSON.errors;
                        console.error(errors);
                        // Clear previous errors
                        // $form.find('.error-message').remove();
                        // // Display new errors
                        // for (const field in errors) {
                        //     const errorMessages = errors[field];
                        //     const $input = $form.find(`[name="${field}"]`);
                        //     errorMessages.forEach(function (message) {
                        //         const $error = $('<div class="error-message text-red-600 text-sm mt-1"></div>').text(message);
                        //         $input.after($error);
                        //     });
                        // }

                        // Display error using ShowNotification

                        const errorHtml = `
                            <div>
                                <p>There were errors saving the template:</p>
                                <ul class="list-disc list-inside">
                                    ${Object.entries(errors).map(([field, messages]) => `
                                        ${Array.isArray(messages) ? messages.map(message => `<li>${message}</li>`).join('') : `<li>${messages}</li>`}
                                    `).join('')}
                                </ul>
                            </div>
                        `;

                        ShowNotification({
                            title: 'Error',
                            // text: 'There were errors saving the template. Please check the form and try again.',
                            html: errorHtml,
                            icon: 'error',
                        });
                    });
            });

            $('#delete-template').on('click', function () {
                const $form = $('#template-form');

                const templateId = $('#template_id').val();
                if (!templateId || templateId === 'new') {
                    ShowNotification({
                        title: 'Error',
                        text: 'Cannot delete a template that has not been created yet.',
                        icon: 'error',
                    });
                    return;
                }

                // Let's complicate this deletion a bit by having the user input the template name to confirm deletion
                // just like GitHub does for repo deletion

                const htmlContent = `
                    <p>Type the template name <strong>"${$form.find('input[name="name"]').val()}"</strong> to confirm deletion:</p>
                    <input type="text" id="confirm-template-name" class="swal2-input" placeholder="Template Name">
                `;

                ShowNotification({
                    title: 'Confirm Deletion',
                    // text: 'Are you sure you want to delete this template? This action cannot be undone.',
                    html: htmlContent,
                    icon: 'warning',
                    confirmButtonText: 'Yes, Delete It',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        const inputName = Swal.getPopup().querySelector('#confirm-template-name').value;
                        if (inputName !== $form.find('input[name="name"]').val()) {
                            Swal.showValidationMessage(`Template name does not match.`);
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Proceed with deletion
                        $.ajax({
                            url: '<?= route_to('delete-package-group-template', $org_slug) ?>',
                            type: 'DELETE',
                            data: {
                                template_id: templateId,
//                                <?php //= csrf_token() ?>//: '<?php //= csrf_hash() ?>//'
                                '<?= csrf_token() ?>': $form.find(`input[name='<?= csrf_token() ?>']`).val()
                            },
                            success: function (response, textStatus, xhr) {

                                const newCsrfToken = xhr.getResponseHeader('X-CSRF-Token');
                                if (newCsrfToken) {
                                    // Update the CSRF token in the form
                                    $form.find(`input[name='<?= csrf_token() ?>']`).val(newCsrfToken);
                                }

                                ShowNotification({
                                    title: 'Deleted',
                                    text: 'The template has been deleted successfully.',
                                    icon: 'success',
                                }).then(() => {
                                    // Redirect to the main templates page
                                    window.location.href = '<?= route_to('package-group-templates', $org_slug) . '?package_id=' . $packageId . '&group_id=new'?>';
                                });
                            },
                            error: function (xhr) {

                                const newCsrfToken = xhr.getResponseHeader('X-CSRF-Token');
                                if (newCsrfToken) {
                                    // Update the CSRF token in the form
                                    $form.find(`input[name='<?= csrf_token() ?>']`).val(newCsrfToken);
                                }

                                ShowNotification({
                                    title: 'Error',
                                    text: 'There was an error deleting the template. Please try again.',
                                    icon: 'error',
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
<?php $this->endSection(); ?>