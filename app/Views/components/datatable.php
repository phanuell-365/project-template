<?php
/**
 * @var string $id
 * @var array $attributes
 * @var bool $datepicker
 * @var array|null $date_filters
 */

$today = date('d/m/Y');

$start_date_input = view('components/form-date-picker', [
        'props' => [
                'id'                  => 'start_date',
                'label'               => 'Start Date',
                'name'                => 'start_date',
                'type'                => 'start_date',
                //                'value'               => $today,
                'value'               => $date_filters['date_from'] ?? $today,
                'datepicker'          => false,
                'datepicker-autohide' => false,
                'datepicker-title'    => 'Start Date',
                'has-range'           => true,
        ]
]);

$end_date_input = view('components/form-date-picker', [
        'props' => [
                'id'                  => 'end_date',
                'label'               => 'End Date',
                'name'                => 'end_date',
                'type'                => 'end_date',
//                'value'               => $today,
                'value'               => $date_filters['date_to'] ?? $today,
                'datepicker'          => false,
                'datepicker-autohide' => false,
                'datepicker-title'    => 'End Date',
                'has-range'           => true,
        ]
]);


?>

<div class="grid grid-cols-1 gap-4">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="range-picker" date-rangepicker>
            <?= $start_date_input ?>
            <?= $end_date_input ?>
        </div>

        <div class="flex items-end justify-end space-x-2">
            <button id="filter-<?= $id ?>-button" class="btn btn-sm btn-primary" type="button">
                <span class="material-symbols-rounded">
                    filter_list
                </span>
                <span>Filter</span>
            </button>
            <button id="reset-<?= $id ?>-button" class="btn btn-sm btn-secondary" type="button">
                <span class="material-symbols-rounded">
                    refresh
                </span>
                <span>Reset</span>
            </button>
        </div>
    </div>

    <table id="<?= $id ?>" class="<?= $id ?>-list table">
        <thead>
        <tr>
            <th data-sortable="false" class="w-12" id="<?= $id ?>-select-all-column">
                <div class="flex items-center justify-center">
                    <label for="checkall_<?= $id ?>" class="sr-only">all</label>
                    <input type="checkbox" name="check_x" id="checkall_<?= $id ?>"
                           class="checkbox checkbox-xs sm:checkbox-sm checkbox-primary">
                </div>
            </th>
            <?php foreach ($attributes as $key => $attribute): ?>
                <?php if ($key === 0) : ?>
                    <th class=""><?= humanize($attribute) ?></th>
                <?php elseif ($key === 1): ?>
                    <th data-sortable="false"><?= humanize($attribute) ?></th>
                <?php else: ?>
                    <th class=""><?= humanize($attribute) ?></th>
                <?php endif; ?>
            <?php endforeach; ?>

            <th data-sortable="false" id="<?= $id ?>-actions-column">Actions</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot class="pt-4 text-sm text-gray-700 bg-gray-50">
        <tr class="">
            <th class="px-4 py-2"></th>
            <?php foreach ($attributes as $column): ?>
                <th class="p-1" scope="col" title="<?= humanize($column) ?>">
                    <?= humanize($column) ?>
                </th>
            <?php endforeach; ?>
            <th class="p-1" scope="col">
                Actions
            </th>
        </tr>
        </tfoot>
    </table>
</div>
