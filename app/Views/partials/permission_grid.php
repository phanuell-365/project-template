<?php
/**
 * @var array $all_permissions
 * @var array $assigned_permission_ids
 */
?>
<div class="space-y-6">
    <?php foreach ($all_permissions as $index => $parent): ?>
        <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200 hover:shadow-md transition-shadow duration-200">
            <!-- Parent Permission Header -->
            <div class="bg-gradient-to-r from-soko-200/60 to-soko-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-soko-600 rounded-lg flex items-center justify-center">
                                <?php if (!empty($parent['icon'])): ?>
                                    <span class="material-symbols-rounded text-white">
                                        <?= esc($parent['icon']) ?>
                                    </span>
                                <?php else: ?>
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <input id="parent_<?= esc($parent['permission_id']) ?>"
                                   name="permissions[]"
                                   value="<?= esc($parent['permission_id']) ?>"
                                   type="checkbox"
                                   class="parent-checkbox h-5 w-5 rounded border-gray-300 text-soko-600 focus:ring-soko-500 cursor-pointer"
                                   data-parent-id="<?= esc($parent['permission_id']) ?>"
                                    <?= in_array($parent['permission_id'], $assigned_permission_ids) ? 'checked' : '' ?>>
                            <label for="parent_<?= esc($parent['permission_id']) ?>"
                                   class="ml-3 cursor-pointer">
                                <span class="text-lg font-semibold text-gray-900"><?= esc($parent['name']) ?></span>
                                <span class="text-sm text-gray-600 mt-1"><?= esc($parent['description']) ?></span>
                            </label>
                        </div>
                    </div>
                    <?php if (!empty($parent['children'])): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-soko-100 text-soko-800">
                        <?= count($parent['children']) ?> child permission<?= count($parent['children']) > 1 ? 's' : '' ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Child Permissions -->
            <?php if (!empty($parent['children'])): ?>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($parent['children'] as $child): ?>
                            <div class="relative flex items-start p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-soko-300 transition-all duration-150">
                                <div class="flex items-center h-5">
                                    <input id="child_<?= esc($child['permission_id']) ?>"
                                           name="permissions[]"
                                           value="<?= esc($child['permission_id']) ?>"
                                           type="checkbox"
                                           class="child-checkbox h-4 w-4 rounded border-gray-300 text-soko-600 focus:ring-soko-500 cursor-pointer"
                                           data-parent-id="<?= esc($parent['permission_id']) ?>"
                                            <?= in_array($child['permission_id'], $assigned_permission_ids) ? 'checked' : '' ?>>
                                </div>
                                <div class="ml-3 text-sm flex-1">
                                    <label for="child_<?= esc($child['permission_id']) ?>"
                                           class="font-medium text-gray-900 cursor-pointer"><?= esc($child['name']) ?></label>
                                    <p class="text-gray-500 text-xs mt-1"><?= esc($child['description']) ?></p>
                                    <?php if (!empty($child['context'])): ?>
                                        <?php if ($child['context'] === 'admin'): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mt-2">
                                            <?= esc($child['context']) ?>
                                    </span>
                                        <?php elseif ($child['context'] === 'app'): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-2">
                                            <?= esc($child['context']) ?>
                                    </span>
                                        <?php elseif ($child['context'] === 'both'): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-2">
                                            <?= esc($child['context']) ?>
                                    </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div><?php
