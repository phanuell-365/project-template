<?php
// app/Views/partials/_permission_tree.php
/**
 * @var array $permissions
 * @var int $level
 */
$level = $level ?? 0;
$marginClass = $level > 0 ? 'ml-' . min($level * 5, 20) : '';

// By default, tailwind will never see classes generated dynamically like above,
// so we need to safelist them in the tailwind config file if we want to use them.
// Alternatively, we could define a fixed set of margin classes here.
// For example:
/*
$marginClasses = [
    0 => 'ml-0',
    1 => 'ml-5',
    2 => 'ml-10',
    3 => 'ml-15',
    4 => 'ml-20',
];
$marginClass = $marginClasses[$level] ?? 'ml-20';
*/

//dd($permissions);
?>

<?php foreach ($permissions as $permission): ?>
    <div class="permission-group mb-2 <?= $marginClass ?>">
        <div class="flex items-start">
            <?php $hasChildren = !empty($permission['children']); ?>
            <input type="checkbox"
                   class="mt-1 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 <?= $hasChildren ? 'permission-parent-checkbox' : '' ?>"
                   name="permissions[]"
                   value="<?= esc($permission['permission_id']) ?>"
                   id="permission_<?= esc($permission['permission_id']) ?>">
            <label class="ml-2 cursor-pointer" for="permission_<?= esc($permission['permission_id']) ?>">
                <span class="text-sm font-medium text-gray-900"><?= esc($permission['name']) ?></span>
                <?php if (!empty($permission['description'])): ?>
                    <span class="block text-xs text-gray-500"><?= esc($permission['description']) ?></span>
                <?php endif; ?>
            </label>
        </div>

        <?php if ($hasChildren): ?>
            <div class="permission-children ml-6 mt-1 pl-2 border-l border-gray-200">
                <?= view('partials/_permission_tree', ['permissions' => $permission['children'], 'level' => $level + 1]) ?>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?><?php
