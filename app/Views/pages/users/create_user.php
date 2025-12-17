<?php
/**
 * @var string|null $org_name
 * @var array $groups
 * @var string $org_slug
 */
$this->extend('layouts/main');

//dd($groups);

// What's needed to create a user?
// - Full name
// - Email
// - Phone number
// - Password (will auto-generate?)
// - Assign to groups (multi-select)
// - Status (active/inactive)

$form_input_controls = [
        'full_name' => [
                'id'          => 'full_name',
                'name'        => 'full_name',
                'type'        => 'text',
                'label'       => 'Full Name',
                'placeholder' => 'Enter full name',
                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
                'required'    => true,
                'helper-text' => 'The full name of the user.',
        ],
        'email'     => [
                'id'          => 'email',
                'name'        => 'email',
                'type'        => 'email',
                'label'       => 'Email Address',
                'placeholder' => 'Enter email address',
                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
                'required'    => true,
                'helper-text' => 'The email address of the user.',
        ],
        'phone'     => [
                'id'          => 'phone',
                'name'        => 'phone',
                'type'        => 'text',
                'label'       => 'Phone Number',
                'placeholder' => 'Enter phone number',
                'outer-class' => 'basis-3/4 md:ml-0 md:mr-2',
                'required'    => false,
                'helper-text' => 'The phone number of the user.',
        ],
];

$groups_options = array_column($groups, 'name', 'id');

$form_select_options = [
        'status' => [
                'id'          => 'status',
                'name'        => 'status',
                'label'       => 'Status',
                'options'     => [
                        '1' => 'Active',
                        '0' => 'Inactive',
                ],
                'outer-class' => 'basis-1/4 md:ml-2 md:mr-0',
                'required'    => true,
                'helper-text' => 'Set the user status to active or inactive.',
                'value'       => '1', // default to active
        ],
        'groups' => [
                'id'          => 'groups',
                'name'        => 'groups[]',
                'label'       => 'Assign to Groups',
                'options'     => $groups_options,
                'outer-class' => 'basis-full md:ml-0 md:mr-0',
                'required'    => false,
                'multiple'    => true,
                'helper-text' => 'Select one or more groups to assign the user to.',
                'value'       => count($groups_options) > 0 ? array_keys($groups_options)[0] : '',
        ],
];
?>

<?php $this->section('title'); ?>
Create User - <?= esc($org_name ?? 'Organization') ?>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<h1 class="text-3xl font-bold text-gray-900 mb-2">
    Create New User
</h1>
<p class="text-gray-600 text-sm">
    Use the form below to create a new user for <?= esc($org_name ?? 'the organization') ?>.
</p>

<div class="grid grid-cols-1 gap-4">
    <?= form_open(route_to('create-user', $org_slug), [
            'class' => 'mt-6 space-y-6',
            'id'    => 'create-user-form'
    ]) ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <?php foreach ($form_input_controls as $input_control): ?>
            <?= view('components/form-input', ['props' => $input_control]) ?>
        <?php endforeach; ?>

        <?php foreach ($form_select_options as $select_control): ?>
            <?= view('components/form-select-option', ['props' => $select_control]) ?>
        <?php endforeach; ?>

        <div class="md:col-span-2">
            <button type="submit"
                    class="btn btn-sm normal-case btn-primary text-white">
                Create User
            </button>
        </div>
    </div>
    <?= form_close() ?>
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

        $('#groups').select2({
            width: '100%',
            theme: 'default',
            placeholder: 'Select groups',
            allowClear: true
        });
    });
</script>

<script>

    // Handle form submission via AJAX
    //$('#create-user-form').on('submit', function (e) {
    //    e.preventDefault();
    //
    //    const form = $(this);
    //    const url = form.attr('action');
    //    const formData = form.serialize();
    //
    //    $.post(url, formData)
    //        .done(function (response) {
    //            // Handle success - show a success message or redirect
    //            alert('User created successfully!');
    //            // Optionally, redirect to the user list page
    //            window.location.href = '<?php //= route_to('users-list', $org_slug) ?>//';
    //        })
    //        .fail(function (xhr) {
    //            // Handle error - show error messages
    //            const errors = xhr.responseJSON.errors;
    //            let errorMessage = 'Error creating user:\n';
    //            for (const field in errors) {
    //                errorMessage += `${errors[field]}\n`;
    //            }
    //            alert(errorMessage);
    //        });
    //});
</script>

<?php $this->endSection(); ?>
