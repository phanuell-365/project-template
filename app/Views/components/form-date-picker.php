<?php
$props = $props ?? [];

// create today's date in the format yyyy/mm/dd
$today = date('d/m/y');

$inputData = [
    'id' => $props['id'] ?? '',
    'name' => $props['name'] ?? '',
    'value' => $props['value'] ?? '',
    'class' => $props['class'] ?? 'w-full pl-10 pr-3 py-2 text-gray-900 placeholder-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring-soko-500 focus:border-soko-500 sm:text-sm',
    'placeholder' => $props['placeholder'] ?? ' ',
    'datepicker' => $props['datepicker'] ?? true,
    'datepicker-autohide' => $props['datepicker-autohide'] ?? true,
    'datepicker-format' => 'dd/mm/yyyy',
    'data-date' => $props['value'] ?? $today,
    'type' => 'text',
];



if (!$props['datepicker-autohide']) {
    unset($inputData['datepicker-autohide']);
}

if (!$props['datepicker']) {
    unset($inputData['datepicker']);
}

// the input is automatically labeled, so we need to remove the label if it is set to false
$hasLabel = $props['has-label'] ?? true;

if ($hasLabel) {
    $labelData = [
        'class' => 'block text-sm font-medium text-soko-600',

        // if the input has a label, set it
        // make the label's text a span, so we can add content after it
        'label' => '<span class="block text-sm font-medium text-soko-600 pb-2">'
            . $props['label']
            . '</span>'
    ];
}

if (isset($props['datepicker-title'])) {
    $inputData['datepicker-title'] = $props['datepicker-title'];
}

if (isset($props['required']) && $props['required'] === true) {
    $inputData['required'] = true;

    // add a red asterisk to the label
    $labelData['label'] = "<span class=\"after:content-['*'] after:ml-0.5 after:text-red-500 after:font-bold block text-sm font-medium text-soko-600 pb-2\">"
        . $props['label']
        . "</span>";
}

if (isset($props['datepicker-buttons'])) {
    $inputData['datepicker-buttons'] = $props['datepicker-buttons'];
}

if (isset($props['disabled']) && $props['disabled'] === true) {
    unset($inputData['required']);
    $inputData['disabled'] = true;
    $inputData['aria-disabled'] = 'true';
    $inputData['readonly'] = true;
    $inputData['class'] .= ' bg-slate-50 cursor-default text-slate-500';
}

$errorFeedback = $props['invalid-feedback'] ?? '';

// check if has-error is set
if (isset($props['has-error']) && $props['has-error'] === true) {
    $errorFeedback = $props['error-feedback'] ?? $props['invalid-feedback'] ?? '';

// append the error class to the input's class if there is an error
    $inputData['class'] .= ' border-pink-600 focus:border-pink-600 focus:ring-pink-600 caret-pink-600 text-pink-500';
}

$has_range = isset($props['has-range']) && $props['has-range'] === true;

?>


<div class="<?= $props['outer-class'] ?? '' ?> relative" id="input-<?= $props['id'] ?? '' ?>">
    <div class="absolute top-8 left-0 flex items-center pl-3 pointer-events-none">
        <span class="material-symbols-rounded text-gray-400 text-lg" aria-hidden="true">date_range</span>
    </div>
    <!--    <label for="date" class="sr-only"> Date </label>-->
    <?php if ($hasLabel) : ?>
        <?= form_label($labelData['label'], $props['id'], ['class' => $labelData['class']]) ?>
    <?php endif; ?>

    <?= view('components/form-input-error', [
        'props' => $props,
        'errorFeedback' => $errorFeedback,
    ]) ?>

    <!--    <input datepicker datepicker-autohide type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date">-->
    <?= form_input($inputData) ?>
</div>
