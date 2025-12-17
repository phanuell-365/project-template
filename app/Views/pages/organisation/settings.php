<?php
/**
 * @var array $sections
 * @var array $section
 * @var array $sectionsHtml
 * @var array $settings_fields
 * @var string $org_name
 * @var string $org_slug
 */

$settings_fields = $section['value'];

$this->extend('layouts/main');

//dd($section);

$form_input_types = [
        'text',
        'number',
        'password',
        'file',
        'email',
        'url',
        'tel',
        'date',
        'time',
        'datetime-local',
        'color',
];

?>
<?php $this->section('title'); ?>
General Settings - <?= esc($org_name ?? 'Organization') ?>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<h1 class="text-3xl font-bold text-gray-900 mb-2">
    General Settings
</h1>
<p class="text-gray-600 text-sm">
    Manage the general settings for <?= esc($org_name ?? 'your organization') ?>.
</p>

<div class="grid grid-cols-1 py-6">
    <?= $sectionsHtml ?>
    <div role="tabpanel" aria-labelledby="<?= esc($section['key']) ?>-tab"
         class="border-b border-x border-gray-200 rounded-b-lg p-4">

        <div class="grid grid-cols-1 gap-4">
            <?= form_open(route_to('save-general-settings', $org_slug) . '?section=' . esc($section['key']), [
                    'class'   => 'mt-6 space-y-6',
                    'id'      => 'general-settings-form',
                    'enctype' => $section['key'] === 'site' ? 'multipart/form-data' : 'application/x-www-form-urlencoded',
            ]) ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($settings_fields as $field): ?>
                    <?php if (in_array($field['type'], $form_input_types)): ?>
                        <?= view('components/form-input', ['props' => $field]) ?>
                    <?php elseif ($field['type'] == 'textarea'): ?>
                        <?= view('components/form-textarea', ['props' => $field]) ?>
                    <?php elseif ($field['type'] == 'select'): ?>
                        <?= view('components/form-select-option', ['props' => $field]) ?>
                    <?php elseif ($field['type'] == 'checkbox'): ?>
                        <?= view('components/form-checkbox', ['props' => $field]) ?>
                    <?php endif; ?>
                <?php endforeach; ?>

            </div>

            <!-- Form Actions -->
            <div class="ml-auto space-x-3 flex items-center">
                <button type="submit"
                        id="reset-btn"
                        name="reset-btn"
                        value="reset-btn"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors text-xs font-bold inline-flex items-center">
                    <span class="material-symbols-rounded mr-2">restart_alt</span>
                    Reset
                </button>
                <button type="submit"
                        name="save-template-btn"
                        value="save-template-btn"
                        id="save-template-btn"
                        class="px-4 py-2 bg-soko-600 text-white rounded-md hover:bg-soko-700 transition-colors text-xs font-bold inline-flex items-center">
                    <span class="material-symbols-rounded mr-2">save</span>
                    <span id="save-btn-text">Save Changes</span>
                </button>
            </div>

            <?= form_close() ?>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php
$this->section('bottom-scripts');
?>

<script type="text/javascript">
    $(document).ready(function () {
        // Make all the selects use select2
        $('select').each(function () {
            $(this).select2({
                width: '100%',
                theme: 'default'
            });
        });

        // $('#general-settings-form').on('submit', function (e) {
        //     e.preventDefault();
        //
        //     const form = $(this);
        //     const url = form.attr('action');
        //     const formData = form.serialize();
        //
        //     $.post(url, formData, function (response) {
        //         if (response.success) {
        //             alert('Settings saved successfully.');
        //         } else {
        //             alert('Error saving settings: ' + response.message);
        //         }
        //     }, 'json').fail(function () {
        //         alert('An unexpected error occurred.');
        //     });
        // });
    });
</script>

<?php $this->endSection(); ?>
