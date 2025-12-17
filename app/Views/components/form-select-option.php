<?php

$props = $props ?? [];

$options = $props['options'] ?? [];

$selectData = [
    'id' => $props['id'] ?? '',
    'class' => 'w-full px-3 py-2 bg-transparent appearance-none border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-soko-500 focus:border-soko-500 block',
];

$helper_text = $props['helper-text'] ?? '';

// the input is automatically labeled, so we need to remove the label if it is set to false
$hasLabel = $props['has-label'] ?? true;

$disableLabelClass = $props['disable-label-class'] ?? false;

if ($hasLabel) {
    $labelData = [
        'class' => $disableLabelClass ? '' :
            'block text-sm font-medium text-soko-600',

        // if the input has a label, set it
        // make the label's text a span, so we can add content after it
        'label' => '<span class="block text-soko-600 pb-2">'
            . $props['label']
            . '</span>'
    ];
}

if (isset($props['required']) && $props['required'] === true) {
    $selectData['required'] = true;

    $asterisk = '*';

    // add a red asterisk to the label
    $labelData['label'] = "<span class=\"after:content-['*'] after:ml-0.5 after:text-red-500 after:font-bold block text-sm font-medium text-soko-600 pb-2\">"
        . $props['label']
        . "</span>";
}

$errorFeedback = $props['invalid-feedback'] ?? '';

// check if has-error is set
if (isset($props['has-error']) && $props['has-error'] === true) {
    $errorFeedback = $props['error-feedback'] ?? $props['invalid-feedback'] ?? '';

// append the error class to the input's class if there is an error
    $selectData['class'] .= ' border-pink-600 focus:border-pink-600 focus:ring-pink-600 caret-pink-600 text-pink-500';
}

if (isset($props['multiple'])) {
    $selectData['multiple'] = 'multiple';
}


// additional classes from the props to the label
if ($hasLabel) {
    $labelData['class'] .= ' ' . ($props['label-class'] ?? '');
}

if (isset($props['disabled']) && $props['disabled'] === true) {
    unset($selectData['required']);
    $selectData['disabled'] = true;
    $selectData['aria-disabled'] = 'true';
    $selectData['readonly'] = true;
    $selectData['class'] .= ' bg-slate-50 cursor-default text-slate-500';
}
?>

<div class="<?= $props['outer-class'] ?? '' ?>">

    <?php if ($hasLabel) : ?>
        <?= form_label($labelData['label'], $props['id'], ['class' => $labelData['class']]) ?>
        <small class="block tracking-tight text-xs text-gray-500"><?= $helper_text ?></small>
    <?php endif; ?>

    <?= view('components/form-input-error', [
        'props' => $props,
        'errorFeedback' => $errorFeedback,
    ]) ?>

    <?= form_dropdown($props['name'], $options, $props['value'], $selectData) ?>
</div>
