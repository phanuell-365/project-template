<?php
$props = $props ?? [];

$inputData = [
    'id' => $props['id'] ?? '',
    'name' => $props['name'] ?? '',
    'value' => $props['value'] ?? old($props['name'] ?? ''),
    'type' => $props['type'] ?? 'text',
    'class' => 'w-full px-3 py-2 text-gray-900 placeholder-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring-soko-500 focus:border-soko-500 sm:text-sm',
    'placeholder' => $props['placeholder'] ?? ' ',
    'autocomplete' => $props['autocomplete'] ?? 'on',
];

$helper_text = $props['helper-text'] ?? '';

// additional classes from the props
$inputData['class'] .= ' ' . ($props['addon-class'] ?? '');

// the input is automatically labeled, so we need to remove the label if it is set to false
$hasLabel = $props['has-label'] ?? true;

$disableLabelClass = $props['disable-label-class'] ?? false;

if ($hasLabel) {
    $labelData = [
        'class' => $disableLabelClass ? '' :
            'block text-sm font-medium text-soko-600',

        // if the input has a label, set it
        // make the label's text a span, so we can add content after it
        'label' => '<span class="block text-soko-600">'
            . $props['label']
            . '</span>'
    ];
}

if (isset($props['required']) && $props['required'] === true) {
    $inputData['required'] = true;

    // add a red asterisk to the label
    $labelData['label'] = "<span class=\"after:content-['*'] after:ml-0.5 after:text-red-500 after:font-bold block text-soko-600\">"
        . $props['label']
        . "</span>";
}


// additional classes from the props to the label
if ($hasLabel) {
    $labelData['class'] .= ' ' . ($props['label-class'] ?? '');
}

if (isset($props['disabled']) && $props['disabled'] === true) {
    unset($inputData['required']);
    $inputData['disabled'] = true;
    $inputData['aria-disabled'] = 'true';
    $inputData['readonly'] = true;
    $inputData['class'] .= ' bg-slate-50 cursor-default text-slate-500';
}

if (isset($props['min'])) {
    $inputData['min'] = $props['min'];
}

$errorFeedback = $props['invalid-feedback'] ?? '';

// check if has-error is set
//if (isset($props['has-error']) && $props['has-error'] === true) {
//    $errorFeedback = $props['error-feedback'] ?? $props['invalid-feedback'] ?? '';

// append the error class to the input's class if there is an error
//    $inputData['class'] .= ' border-pink-600 focus:border-pink-600 focus:ring-pink-600 caret-pink-600 text-pink-500';
//}

// pick all the errors from the 'errors' key in props
$errors = $props['errors'] ?? [];

if (isset($errors[$inputData['name']])) {
//    dd('here', $errors, $inputData['name']);
    // there is an error for this input
    $errorFeedback = $errors[$inputData['name']];
    // append the error class to the input's class
    $inputData['class'] .= ' border-pink-600 focus:border-pink-600 focus:ring-pink-600 caret-pink-600 text-pink-500';
}

// let's create a helper text for the input

if ($helper_text === '') {
    // if the helper text is not set, let's set it based on the input type
    // (this is not a complete list of input types, but it's a start :)
    match ($inputData['type']) {
        'email' => $helper_text = 'Ex: johndoe23@gmail.com',
        'password' => $helper_text = 'Must be at least 8 characters',
        'number' => $helper_text = 'Ex: 1234567890',
        'tel' => $helper_text = 'Ex: 0700000000 or 0100000000',
        'url' => $helper_text = 'Ex: https://www.example.com',
        'date' => $helper_text = 'Ex: 2021-06-01',
        'datetime-local' => $helper_text = 'Ex: 2021-06-01T13:45',
        'time' => $helper_text = 'Ex: 13:45',
        'week' => $helper_text = 'Ex: 2021-W01',
        'month' => $helper_text = 'Ex: 2021-06',
        'search' => $helper_text = 'Ex: John Doe',
        'color' => $helper_text = 'Ex: #ff0000',
        default => $helper_text = '',
    };

    // if the helper text is not empty, let's add a period at the end
    if ($helper_text !== '') {
        $helper_text .= '.';
    }
}

?>

<div class="<?= $props['outer-class'] ?? '' ?> space-y-1" id="input-<?= $props['id'] ?? '' ?>">
    <?php if ($hasLabel) : ?>
        <?= form_label($labelData['label'], $props['id'], ['class' => $labelData['class']]) ?>
        <small class="block tracking-tight text-xs text-gray-500"><?= $helper_text ?></small>
    <?php endif; ?>

    <?= view('components/form-input-error', [
        'props' => $props,
        'errorFeedback' => $errorFeedback,
    ]) ?>

    <?= form_input($inputData) ?>
</div>
