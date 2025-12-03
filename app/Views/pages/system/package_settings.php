<?php

$this->extend('layouts/main');

$org_slug = $org_slug ?? '';
$packages = $packages ?? [];

$form_input_controls = [
        'name'          => [
                'id'          => 'name',
                'label'       => 'Name',
                'name'        => 'name',
                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
                'type'        => 'text',
                'required'    => true,
                'placeholder' => 'Standard Package',
                'helper-text' => 'A descriptive name for the package.',
        ],
        'price'         => [
                'id'          => 'price',
                'label'       => 'Price',
                'name'        => 'price',
                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
                'type'        => 'number',
                'required'    => true,
                'min'         => 0,
                'placeholder' => '1000',
        ],
        'duration_days' => [
                'id'          => 'duration_days',
                'label'       => 'Duration (Days)',
                'name'        => 'duration_days',
                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
                'type'        => 'number',
                'required'    => true,
                'min'         => 1,
                'placeholder' => '30',
        ],
        'features'      => [
                'id'          => 'features',
                'label'       => 'Features',
                'name'        => 'features',
                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
                'type'        => 'text',
                'required'    => false,
                'placeholder' => 'Feature1, Feature2, Feature3',
                'helper-text' => 'Comma-separated list of features included in this package.',
        ],
        'max_users'     => [
                'id'          => 'max_users',
                'label'       => 'Max Users',
                'name'        => 'max_users',
                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
                'type'        => 'number',
                'required'    => false,
                'min'         => 1,
                'placeholder' => '50',
                'helper-text' => 'Maximum number of users allowed for this package. Leave blank for unlimited.',
        ]
];

//$name_input = view('components/form-input', [
//        'props' => [
//                'id'          => 'name',
//                'label'       => 'Name',
//                'name'        => 'name',
//                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
//                'type'        => 'text',
//                'required'    => true,
//                'placeholder' => 'Standard Package',
//                'helper-text' => 'A descriptive name for the package.',
//        ]
//]);
//
//$price_input = view('components/form-input', [
//        'props' => [
//                'id'          => 'price',
//                'label'       => 'Price',
//                'name'        => 'price',
//                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
//                'type'        => 'number',
//                'required'    => true,
//                'min'         => 0,
//                'placeholder' => '1000',
//        ]
//]);
//
//$duration_input = view('components/form-input', [
//        'props' => [
//                'id'          => 'duration_days',
//                'label'       => 'Duration (Days)',
//                'name'        => 'duration_days',
//                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
//                'type'        => 'number',
//                'required'    => true,
//                'min'         => 1,
//                'placeholder' => '30',
//        ]
//]);
//
//$features_input = view('components/form-input', [
//        'props' => [
//                'id'          => 'features',
//                'label'       => 'Features',
//                'name'        => 'features',
//                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
//                'type'        => 'text',
//                'required'    => false,
//                'placeholder' => 'Feature1, Feature2, Feature3',
//                'helper-text' => 'Comma-separated list of features included in this package.',
//        ]
//]);
//
//$max_users_input = view('components/form-input', [
//        'props' => [
//                'id'          => 'max_users',
//                'label'       => 'Max Users',
//                'name'        => 'max_users',
//                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
//                'type'        => 'number',
//                'required'    => false,
//                'min'         => 1,
//                'placeholder' => '50',
//                'helper-text' => 'Maximum number of users allowed for this package. Leave blank for unlimited.',
//        ]
//]);
//

$form_select_options = [
        'status' => [
                'id'          => 'status',
                'label'       => 'Status',
                'name'        => 'status',
                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
                'required'    => true,
                'options'     => [
                        'active'   => 'Active',
                        'inactive' => 'Inactive',
                ],
                'value'       => 'active',
        ]
];

//$status_select = view('components/form-select-option', [
//        'props' => [
//                'id'          => 'status',
//                'label'       => 'Status',
//                'name'        => 'status',
//                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
//                'required'    => true,
//                'options'     => [
//                        'active'   => 'Active',
//                        'inactive' => 'Inactive',
//                ],
//                'value'       => 'active',
//        ]
//]);

$form_textarea_controls = [
        'description' => [
                'id'          => 'description',
                'label'       => 'Description',
                'name'        => 'description',
                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
                'required'    => false,
                'placeholder' => 'A brief description of the package.',
                'rows'        => 4,
                'cols'        => 50,
        ]
];

//$description_textarea = view('components/form-textarea', [
//        'props' => [
//                'id'          => 'description',
//                'label'       => 'Description',
//                'name'        => 'description',
//                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
//                'required'    => false,
//                'placeholder' => 'A brief description of the package.',
//                'rows'        => 4,
//                'cols'        => 50,
//        ]
//]);

?>

<?php $this->section('title'); ?>
    Package Settings - <?= esc($org_name ?? 'Organization') ?>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
    <div class="grid grid-cols-1 gap-4">
        <?= form_open(route_to('create-package-settings', $org_slug), ['class' => 'mt-6 space-y-6']) ?>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Create New Package</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!--            --><?php //= $name_input ?>
            <!---->
            <!--            --><?php //= $price_input ?>
            <!---->
            <!--            --><?php //= $duration_input ?>
            <!---->
            <!--            --><?php //= $features_input ?>
            <!---->
            <!--            --><?php //= $max_users_input ?>
            <!---->
            <!--            --><?php //= $status_select ?>
            <!---->
            <!--            --><?php //= $description_textarea ?>

            <?php foreach ($form_input_controls as $input_control): ?>
                <?= view('components/form-input', ['props' => $input_control]) ?>
            <?php endforeach; ?>

            <?php foreach ($form_select_options as $select_control): ?>
                <?= view('components/form-select-option', ['props' => $select_control]) ?>
            <?php endforeach; ?>

            <?php foreach ($form_textarea_controls as $textarea_control): ?>
                <?= view('components/form-textarea', ['props' => $textarea_control]) ?>
            <?php endforeach; ?>

            <!--    --><?php //= form_hidden('slug', url_title(old('name') ?? 'package-' . time(), '-', true)) ?>

            <div class="md:col-span-2">
                <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-soko-600 hover:bg-soko-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-soko-500">
                    Create Package
                </button>
            </div>
        </div>
        <?= form_close() ?>

        <!-- Existing Packages List -->
        <div class="mt-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Existing Packages</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php if (!empty($packages)): ?>
                    <?php foreach ($packages as $package): ?>
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex flex-col space-y-2">
                                        <h3 class="text-xl font-bold text-gray-900"><?= esc($package['name']) ?></h3>
                                        <!-- description -->
                                        <p class="text-gray-600 mt-1"><?= esc($package['description']) ?></p>
                                        <p class="text-3xl font-bold text-soko-600 mt-2">
                                            Ksh. <?= number_format($package['price'], 2) ?><span
                                                    class="text-sm text-gray-500">/<?= ((int)$package['duration_days'] === 0 ? 'Unlimited' : $package['duration_days'] . ' days') ?></span>
                                        </p>
                                    </div>
                                    <button class="p-2 bg-primary hover:bg-primary/60 rounded-lg transition-colors"
                                            aria-label="Edit Package Settings" data-package-id="<?= $package['id'] ?>"
                                            data-package-data='<?= json_encode($package) ?>'
                                    >
                                        <span class="material-symbols-rounded text-white">
                                            edit
                                        </span>
                                    </button>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                                    <div><p class="text-sm text-gray-500">Organizations</p>
                                        <p class="text-2xl font-semibold text-gray-900"><?= $package['organization_count'] ?></p>
                                    </div>
                                    <div><p class="text-sm text-gray-500">Permissions</p>
                                        <p class="text-2xl font-semibold text-gray-900"><?= $package['permission_count'] ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 bg-gray-50">
                                <button class="w-full flex items-center justify-center gap-2 py-2 text-soko-600 hover:text-soko-700 font-medium">
                                    Manage Permissions
                                    <!--                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"-->
                                    <!--                                         fill="none"-->
                                    <!--                                         stroke="currentColor" stroke-width="2" stroke-linecap="round"-->
                                    <!--                                         stroke-linejoin="round"-->
                                    <!--                                         class="lucide lucide-chevron-right">-->
                                    <!--                                        <path d="m9 18 6-6-6-6"></path>-->
                                    <!--                                    </svg>-->
                                    <span class="material-symbols-rounded">
                                        arrow_forward_ios
                                    </span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-600">No packages found. Please create a new package.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

<?php $this->endSection(); ?>

<?php
$this->section('bottom-scripts');
?>


    <script id="select2-init-script" type="text/javascript">
        // enhance all select elements with select2
        $(document).ready(function () {
            $('#status').select2({
                width: '100%',
                theme: 'default'
            });

        });
    </script>

    <script>
        // we'll be using jQuery for our implementations

        $(document).ready(function () {
            const editButtons = $('button[aria-label="Edit Package Settings"]');
            // Handle Edit Package button click
            editButtons.on('click', async function () {
                const that = this;

                const packageId = $(this).data('package-id');
                // Redirect to the edit page (you need to create this route)
                //window.location.href = `/<?php //= esc($org_slug) ?>///package-settings/edit/${packageId}`;

                // use ShowNotification() a functio that wraps SweetAlert2 to show the edit modal

                <?php
                // We'll prepare the form inputs here to be injected into the Swal modal
                $formInputsHtml = '';

                foreach ($form_input_controls as $index => $input_control) {
                    $input_control['id'] = 'swal-input' . $index;
                    $formInputsHtml .= view('components/form-input', ['props' => $input_control]);
                }

                foreach ($form_select_options as $index => $select_control) {
                    $select_control['id'] = 'swal-input' . $index;
                    $formInputsHtml .= view('components/form-select-option', ['props' => $select_control]);
                }

                foreach ($form_textarea_controls as $index => $textarea_control) {
                    $textarea_control['id'] = 'swal-input' . $index;
                    $formInputsHtml .= view('components/form-textarea', ['props' => $textarea_control]);
                }

                ?>

                const {isConfirmed, value: formValues} = await ShowNotification({
                    title: 'Edit Package Settings',
                    html: `
                        <form id="create-group-form" class="flex flex-col items-center justify-center space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                                <?= $formInputsHtml ?>
                            </div>
                        </form>
                    `,
                    customClass: {
                        popup: 'relative mx-auto flex flex-col w-11/12 sm:w-9/12 md:w-8/12 lg:w-7/12 xl:w-1/23 h-auto bg-white rounded-[28px] shadow-lg',
                    },
                    allowOutsideClick: false,
                    focusConfirm: false,
                    didOpen: () => {
                        const packageData = $(that).data('package-data');

                        $.each(packageData, function (key, value) {
                            const inputElement = $('#swal-input' + key);
                            if (inputElement.length) {
                                if (key === 'status') {
                                    // for select2, we need to set the value and trigger change
                                    value = value === '1' || value === 1 || value === 'active' ? 'active' : 'inactive';
                                    inputElement.val(value).trigger('change');
                                } else if (key === 'features') {
                                    // features comes as an array, convert to comma-separated string
                                    if (Array.isArray(value)) {
                                        inputElement.val(value.join(', '));
                                    }
                                } else {
                                    inputElement.val(value);
                                }
                            }
                        });

                        // initialize select2 for the status select
                        $('#swal-inputstatus').select2({
                            width: '100%',
                            theme: 'default'
                        });
                    },
                    preConfirm: () => {
                        const formData = {};
                        $('#create-group-form').serializeArray().forEach(({name, value}) => {
                            formData[name] = value;
                            formData['package_id'] = packageId; // include package ID
                        });

                        // add the csrf token
                        formData['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';

                        return formData;
                    },
                    confirmButtonText: 'Save Changes',
                    cancelButtonText: 'Cancel',
                    showCancelButton: true,
                });

                if (isConfirmed) {
                    // Send the updated data to the server via AJAX
                    $.ajax({
                        url: `<?= route_to('edit-package-settings', esc($org_slug)) ?>`,
                        method: 'PUT',
                        data: formValues,
                        success: function (response) {
                            console.debug('response', response);
                            // Handle success (e.g., show a success message, update the UI)
                            ShowNotification({
                                title: 'Success',
                                text: 'Package updated successfully.',
                                icon: 'success',
                            }).then(() => {
                                // Optionally, reload the page or update the package list
                                location.reload();
                            });
                        },
                        error: function (xhr, status, error) {
                            // Handle error (e.g., show an error message)
                            ShowNotification({
                                title: 'Error',
                                text: 'An error occurred while updating the package. Please try again.',
                                icon: 'error',
                            });
                        }
                    });
                }
            });
        });
    </script>

<?php
$this->endSection();
?>