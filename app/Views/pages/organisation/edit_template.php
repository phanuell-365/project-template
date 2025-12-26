<?php

/**
 * @var array $template
 * @var string $org_name
 * @var string $org_slug
 */

//dd($template);

use App\Services\TemplateService;

$this->extend('layouts/main');


$form_input_controls = [
        'subject' => [
                'id'          => 'subject',
                'name'        => 'subject',
                'type'        => 'text',
                'label'       => 'Email Subject',
                'placeholder' => 'Enter email subject',
                'outer-class' => '',
                'required'    => true,
                'helper-text' => 'The subject of the email template.',
                'value'       => $template['subject'] ?? '',
        ],
];

$form_textarea_controls = [
        'body' => [
                'id'          => 'body',
                'name'        => 'body',
                'type'        => 'textarea',
                'label'       => 'SMS Body',
                'placeholder' => 'Enter SMS body',
                'outer-class' => '',
                'required'    => true,
                'helper-text' => 'The body of the sms template.',
                'value'       => $template['body'] ?? '',
                'resizable'   => true,
        ]
];

?>

<?php $this->section('title'); ?>
Edit Template - <?= esc($org_name ?? 'Organization') ?>
<?php $this->endSection(); ?>

<?= $this->section('links') ?>
<?= link_tag(base_url('css/quill/quill.snow.css')) ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= script_tag(base_url('js/quill/quill.js')) ?>
<?= $this->endSection() ?>

<?php $this->section('content'); ?>

<h1 class="text-3xl font-bold text-gray-900 mb-2">
    Edit <?= esc($template['name']) ?> Template
    (<?= esc(ucfirst($template['channel'] === TemplateService::EMAIL_CHANNEL || $template['channel'] === TemplateService::RAW_EMAIL_CHANNEL ? 'email' : 'sms')) ?>
    )
</h1>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-y-4 lg:gap-4">
    <div class="col-span-7">
        <div class="flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl p-6">
            <form action="<?= route_to('edit-communication-templates', $org_slug) . '?slug=' . $template['slug'] . '&channel=' . $template['channel'] ?>"
                  method="post"
                  class="space-y-6">
                <?= csrf_field() ?>

                <?php if ($template['channel'] === TemplateService::EMAIL_CHANNEL || $template['channel'] == TemplateService::RAW_EMAIL_CHANNEL): ?>
                    <?php foreach ($form_input_controls as $input_control): ?>
                        <?= view('components/form-input', ['props' => $input_control]) ?>
                    <?php endforeach; ?>

                    <div>
                        <label for="body" class="block text-sm font-medium text-soko-600">
                            <span class="after:content-['*'] after:ml-0.5 after:text-red-500 after:font-bold block text-soko-600">Email Body</span>
                        </label>
                        <div id="body-editor" class="h-64 bg-white border border-gray-300 rounded-lg">
                            <?= ($template['body']) ?>
                        </div>

                        <?= form_input([
                                'type'  => 'hidden',
                                'id'    => 'body',
                                'name'  => 'body',
                                'value' => $template['body'] ?? '',
                        ]); ?>

                        <p class="mt-2 text-xxs text-gray-500">
                            Use the editor above to customize the email body. You can use HTML formatting and
                            placeholders.
                        </p>
                    </div>
                <?php else: ?>
                    <?php foreach ($form_textarea_controls as $textarea_control): ?>
                        <?= view('components/form-textarea', ['props' => $textarea_control]) ?>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="flex items-center justify-between">
                    <!-- have the preview and save button -->
                    <button class="btn btn-sm md:btn-md normal-case btn-secondary text-white"
                            type="button"
                            id="preview-email-btn">
                        <span class="material-symbols-rounded mr-2">preview</span>
                        Preview
                    </button>

                    <button type="submit"
                            class="btn btn-sm md:btn-md normal-case btn-primary text-white">
                        <span class="material-symbols-rounded mr-2">save</span>
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-span-5">
        <!-- Use the above card component example to display the commented code above this component -->

        <div id="available-placeholders" class="flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl">
            <div class="border-b border-gray-200 rounded-t-xl py-3 px-4 md:py-4 md:px-5">
                <p class="mt-1 text-sm text-gray-500">
                    Available Placeholders
                </p>
            </div>
            <div class="bg-gray-100 border-b border-gray-200 text-sm text-gray-800 px-4 md:px-5 py-3 md:py-4 grid grid-cols-1 gap-2">
                        <span class="font-bold">
                            Attention needed!
                        </span>
                You can use the following placeholders in your template. They will be replaced with actual values when
                the communication is sent.
                <span class="italic font-semibold">Do not modify the placeholder names.</span>
            </div>
            <div class="p-4 md:p-5">
                <ul class="list-none space-y-3 max-h-72 overflow-y-auto">
                    <?php foreach ($template['placeholders'] as $placeholder => $description): ?>
                        <li class="space-y-1">
                            <!--                                    <span class="font-mono text-sm text-gray-800">{{-->
                            <?php //= esc($placeholder) ?><!--}}</span>-->
                            <div class="flex items-center space-x-2">
                                        <span class="font-mono text-sm text-gray-800 cursor-pointer hover:underline copy-btn"
                                              data-var="{{<?= esc($placeholder) ?>}}"
                                        >{{<?= esc($placeholder) ?>}}</span>
                                <button type="button" class="text-gray-500 hover:text-gray-700 copy-btn"
                                        title="Copy to clipboard" data-var="{{<?= esc($placeholder) ?>}}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-sm text-gray-600"><?= esc($description) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <p class="px-4 pb-4 text-xxs text-gray-500">
                Click on a placeholder to copy it to clipboard.
            </p>
        </div>
    </div>
</div>

<!-- Preview iframe for the email template -->
<div class="w-full mt-6">
    <div class="flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
            Email Template Preview
        </h2>
        <iframe id="email-preview-iframe" class="w-full h-96 border border-gray-300 rounded-lg"
                srcdoc="<?= esc($template['body']) ?>">
        </iframe>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('bottom-scripts'); ?>
<script>
    $(document).ready(function () {
        // Copy to clipboard functionality for placeholders
        $('.copy-btn').on('click', function () {
            const placeholder = $(this).data('var');

            // Check if the Clipboard API is supported
            if (!navigator.clipboard) {
                ShowNotification({
                    title: 'Error',
                    text: 'Clipboard API not supported in this browser.',
                    icon: 'warning',
                });

                return;
            }

            navigator.clipboard.writeText(placeholder).then(function () {
                // alert('Copied to clipboard: ' + placeholder);
                ShowNotification({
                    title: 'Copied!',
                    text: 'Placeholder ' + placeholder + ' copied to clipboard.',
                    icon: 'success',
                    timer: 2000,
                });
            }, function (err) {
                console.error('Could not copy text: ', err);
            });
        });

        const $form = $('form');

        <?php if ($template['channel'] === TemplateService::EMAIL_CHANNEL || $template['channel'] == TemplateService::RAW_EMAIL_CHANNEL): ?>
        // Initialize Quill editor for the body textarea
        const quill = new Quill('#body-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    ['link', 'blockquote', 'code-block'],
                    [{'indent': '-1'}, {'indent': '+1'}],
                    [{'size': ['small', false, 'large', 'huge']}],
                    [{'list': 'ordered'}, {'list': 'bullet'}],
                    [{'header': [1, 2, 3, 4, 5, 6, false]}],
                    [{'color': []}, {'background': []}],
                    [{'align': []}],
                    ['clean']
                ]
            },
        });

        //quill.root.innerHTML = `<?php //= addslashes($template['body']) ?>//`;

        quill.on('text-change', function () {
            const quillContent = quill.root.innerHTML;
            $('#body').val(quillContent);
        });

        // On form submit, update the textarea with Quill content
        $form.on('submit', function () {
            const quillContent = quill.root.innerHTML;
            $('#body').val(quillContent);

            return true; // Proceed with form submission
        });
        <?php endif; ?>

        const $previewIframe = $('#email-preview-iframe');
        const previewIframe = $previewIframe[0];

        <?php if ($template['channel'] !== TemplateService::EMAIL_CHANNEL && $template['channel'] != TemplateService::RAW_EMAIL_CHANNEL): ?>
        const $body = $('#body');

        // Add restrictions and warning to the SMS body textarea
        $body.on('input', function () {
            const maxLength = 160;
            let currentLength = $body.val().length;

            if (currentLength > maxLength) {
                $body.val($body.val().substring(0, maxLength));
                currentLength = maxLength;
                ShowNotification({
                    title: 'Warning',
                    text: 'SMS body cannot exceed ' + maxLength + ' characters.',
                    icon: 'warning',
                    timer: 2000,
                });
            }

            // Also format the textarea to show current length
            $body.attr('data-length', currentLength + '/' + maxLength);

            // Preserve newlines in the textarea
            const formattedValue = $body.val().replace(/\n/g, '\n');
            $body.val(formattedValue);
        });

        // Add newline when user types 'Enter' key
        $body.on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const cursorPos = this.selectionStart;
                const textBefore = $body.val().substring(0, cursorPos);
                const textAfter = $body.val().substring(cursorPos);
                $body.val(textBefore + '\n' + textAfter);
                this.selectionStart = this.selectionEnd = cursorPos + 1;
            }
        });

        <?php endif; ?>

        // Preview email button click
        $('#preview-email-btn').on('click', function () {
            // Get the current content from Quill editor and fetch preview from the server
            <?php if ($template['channel'] === TemplateService::EMAIL_CHANNEL || $template['channel'] == TemplateService::RAW_EMAIL_CHANNEL): ?>
            const bodyContent = quill.root.innerHTML;
            <?php else: ?>
            const bodyContent = $('#body').val();
            <?php endif; ?>

            $.ajax({
                url: `<?= route_to('preview-communication-template', $org_slug) ?>?slug=<?= $template['slug'] ?>&channel=<?= $template['channel'] ?>`,
                method: 'POST',
                data: {
                    body: bodyContent,
                    '<?= csrf_token() ?>': $form.find(`input[name='<?= csrf_token() ?>']`).val()
                },
                success: function (response, textStatus, xhr) {


                    const newCsrfToken = xhr.getResponseHeader('X-CSRF-Token');
                    if (newCsrfToken) {
                        // Update the CSRF token in the form
                        $form.find(`input[name='<?= csrf_token() ?>']`).val(newCsrfToken);
                    }

                    ShowNotification({
                        title: 'Success',
                        text: 'Email preview updated.',
                        icon: 'success',
                        timer: 2000,
                    });

                    if (!response.success) {
                        ShowNotification({
                            title: 'Error',
                            text: response.error || 'Failed to load email preview.',
                            icon: 'error',
                        });
                        return;
                    }

                    // Update the iframe srcdoc with the preview content
                    // $('#email-preview-iframe').attr('srcdoc', response.body);
                    previewIframe.srcdoc = response.body;
                },
                error: function (xhr, textStatus, errorThrown) {
                    const newCsrfToken = xhr.getResponseHeader('X-CSRF-Token');
                    if (newCsrfToken) {
                        // Update the CSRF token in the form
                        $form.find(`input[name='<?= csrf_token() ?>']`).val(newCsrfToken);
                    }

                    ShowNotification({
                        title: 'Error',
                        text: 'Failed to load email preview.',
                        icon: 'error',
                    });
                }
            });
        });

    });
</script>
<?php $this->endSection(); ?>
