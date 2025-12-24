<?php
$props = $props ?? [];

//$errorFeedback = $errorFeedback ?? '';

$errorFeedbackClass = !empty($errorFeedback) ? 'flex' : 'hidden';

//dd('here', $errorFeedback, $errorFeedbackClass);
?>

<small class="mb-2 sm:flex-row flex-col items-start sm:items-center text-pink-600 dark:text-pink-500 text-sm font-medium <?= $errorFeedbackClass ?>"
       id="<?= $props['id'] ?? '' ?>_invalid_feedback">
        <span class="flex items-center">
            <span class="material-symbols-rounded mr-2">
                error
            </span>
            <span class="text-sm error-feedback-text">
                <?= $errorFeedback ?>
            </span>
        </span>
    <!--  provide an overriding button to hide the error  -->
    <button type="button" class="ml-3 focus:outline-none"
            onclick="
                    (() => {
                    const el = $('#<?= $props['id'] ?? '' ?>');
                    const invalidFeedback = $('#<?= $props['id'] ?? '' ?>_invalid_feedback');
                    let invalidInputClass = 'border-pink-600 focus:border-pink-600 focus:ring-pink-600 caret-pink-600 text-pink-500';

                    el.removeClass(invalidInputClass);
                    invalidFeedback.addClass('hidden');
                    })()">
        <span class="sr-only">Close</span>
        <span class="underline text-sm text-blue-600 hover:text-blue-800 hover:no-underline">
            Hide
        </span>
    </button>
</small>
