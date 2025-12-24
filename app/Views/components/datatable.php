<?php
/**
 * @var string $id
 * @var array $attributes
 */
?>

<div class="grid grid-cols-1 gap-4">
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
