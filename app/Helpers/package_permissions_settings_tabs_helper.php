<?php

/**
 * Generate HTML for package permissions settings tabs.
 *
 * @param string $activeTab The currently active tab key.
 * @param string $org_slug The organization slug for routing.
 * @param int $packageId The package ID for routing.
 * @param array $extraParams Additional parameters to be used as query strings.
 *
 * @return string The generated HTML for the tabs.
 */
function package_permissions_settings_tabs(string $activeTab, string $org_slug, int $packageId, array $extraParams = []): string
{
    $tabs = [
        'package_permissions'     => 'Package Permission',
        'package_group_templates' => 'Group Templates',
    ];

    $html = '<div class="border-b border-gray-200">
                <nav class="flex gap-x-1" aria-label="Tabs" role="tablist" aria-orientation="horizontal">';

    // Create query string from extraParams
    $queryString = http_build_query($extraParams);

    foreach ($tabs as $key => $label) {
        $isActive = ($key === $activeTab) ? 'active' : '';
        $isActiveClass = ($key === $activeTab) ? 'bg-white border-b-transparent text-soko-600' : '';
        $html .= '<a href="' . route_to('get-' . str_replace('_', '-', $key), $org_slug) . '?package_id=' . $packageId . ($queryString ? '&' . $queryString : '') . '"
                    class="-mb-px py-3 px-4 inline-flex items-center gap-x-2 bg-gray-50 text-sm font-medium text-center border border-gray-200 text-gray-500 rounded-t-lg hover:text-gray-700 focus:outline-hidden focus:text-gray-700 disabled:opacity-50 disabled:pointer-events-none' . $isActive . ' ' . $isActiveClass . '"
                    id="card-type-tab-item-' . ($key === 'package_permissions' ? '1' : '2') . '" aria-selected="' . ($isActive ? 'true' : 'false') . '" data-hs-tab="#' . $key . '-tab"
                    aria-controls="' . $key . '-tab" role="tab">
                ' . esc($label) . '
            </a>';
    }

    $html .= '</nav></div>';

    return $html;
}