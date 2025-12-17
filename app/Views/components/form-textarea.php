<?php
$props = $props ?? [];

$resizeClass = isset($props['resizable']) ? 'resize-y' : 'resize-none';

$textarea = [
    'id' => $props['id'] ?? '',
    'name' => $props['name'] ?? '',
    'value' => $props['value'] ?? old($props['name'] ?? ''),
    'class' => "w-full px-3 py-2 text-gray-900 placeholder-gray-400 border border-gray-300 rounded-md focus:outline-none focus:ring-soko-500 focus:border-soko-500 focus:z-10 sm:text-sm " . $resizeClass,
    'placeholder' => $props['placeholder'] ?? '',
    'rows' => $props['rows'] ?? 3,
    'cols' => $props['cols'] ?? 20,
];

$helper_text = $props['helper-text'] ?? '';

$disableLabelClass = $props['disable-label-class'] ?? false;

// the input is automatically labeled, so we need to remove the label if it is set to false
$hasLabel = $props['has-label'] ?? true;

if ($hasLabel) {
    $labelData = [
        'class' => 'block text-sm font-medium text-soko-600',

        // make the label's text a span, so we can add content after it
        'label' => '<span class="block text-sm font-medium text-soko-600 pb-2">'
            . $props['label']
            . '</span>'
    ];
}

if (isset($props['required']) && $props['required'] === true) {
    $textarea['required'] = true;

// add a red asterisk to the label
    $labelData['label'] = "<span class=\"after:content-['*'] after:ml-0.5 after:text-red-500 after:font-bold block text-sm font-medium text-soko-600 pb-2\">"
        . $props['label']
        . "</span>";

}

if (isset($props['disabled']) && $props['disabled'] === true) {
    unset($textarea['required']);
    $textarea['disabled'] = true;
    $textarea['aria-disabled'] = 'true';
    $textarea['readonly'] = true;
    $textarea['class'] .= ' bg-slate-50 cursor-default text-slate-500';
}


$errorFeedback = $props['invalid-feedback'] ?? '';

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

    <?= form_textarea($textarea) ?>
</div>
