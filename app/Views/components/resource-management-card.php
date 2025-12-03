<?php
//dd('here', $props);
[
        'id'       => $id,
        'icon'     => $icon,
        'desc'     => $desc,
        'text'     => $text,
        'sub_text' => $sub_text,
        'link'     => $link
] = $props ?? [];
?>
<div>
    <div class="border border-gray-200 rounded-lg p-4 flex flex-col items-center justify-center gap-3 hover:shadow-lg">
        <div class="h-20 w-20 flex items-center justify-center rounded-full bg-soko-50 p-4 shadow-lg">
            <span class="material-symbols-rounded text-4xl text-soko-600">admin_panel_settings</span>
        </div>
        <h3 class="text-lg font-semibold text-gray-800" id="<?= $id ?>-group-description">
            <?= $desc ?>
        </h3>
        <p class="text-sm font-medium text-gray-500" id="<?= $id ?>-group-primary-name">
            <!--        --><?php //= $group['primary_name'] ?>
            <?= $text ?>
        </p>
        <p class="text-sm font-medium text-gray-500 hidden" id="<?= $id ?>-group-name">
            <!--        --><?php //= $group['name'] ?>
            <?= $sub_text ?>
        </p>
        <div class="flex flex-col gap-2">
            <div class="grid grid-cols-2 gap-2">
                <button data-group-id="<?= $id ?>"
                        class="btn btn-primary normal-case text-white btn-md edit-group">
                    <span class="material-symbols-rounded">edit</span>
                    <span>Edit</span>
                </button>
                <button data-group-id="<?= $id ?>"
                        class="btn btn-error normal-case text-white btn-md delete-group">
                    <span class="material-symbols-rounded">delete</span>
                    <span>Delete</span>
                </button>
            </div>
            <!--        <a href="--><?php //= base_url('/packages/' . $package['id'] . '/' . $id) ?><!--"-->

            <a href="<?= base_url($link) ?>"
               class="btn btn-success normal-case text-white btn-md">
                <span class="material-symbols-rounded">security</span>
                <span>Manage Permissions</span>
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-start justify-between mb-4">
                <div><h3 class="text-xl font-bold text-gray-900">Starter</h3>
                    <p class="text-3xl font-bold text-soko-600 mt-2">$0<span class="text-sm text-gray-500">/mo</span>
                    </p>
                </div>
                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="lucide lucide-pen text-gray-600">
                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                <div><p class="text-sm text-gray-500">Organizations</p>
                    <p class="text-2xl font-semibold text-gray-900">45</p></div>
                <div><p class="text-sm text-gray-500">Permissions</p>
                    <p class="text-2xl font-semibold text-gray-900">12</p></div>
            </div>
        </div>
        <div class="p-4 bg-gray-50">
            <button class="w-full flex items-center justify-center gap-2 py-2 text-soko-600 hover:text-soko-700 font-medium">
                Manage Permissions
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-chevron-right">
                    <path d="m9 18 6-6-6-6"></path>
                </svg>
            </button>
        </div>
    </div>
</div>
