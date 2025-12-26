<?php

/**
 * @var array $templates
 * @var array $attributes
 * @var string $org_slug
 */

//dd($templates);

$this->extend('layouts/main');
?>

<?= $this->section('scripts') ?>
<?= view('components/links') ?>
<?= $this->endSection() ?>

<?php $this->section('content'); ?>
<?= view('components/datatable', [
    'id' => 'companies-table',
    'attributes' => $attributes
]) ?>
<?= $this->endSection() ?>


<?= $this->section('bottom-scripts') ?>
<!--    <script src="--><?php //= base_url('js/table-utils.js') ?><!--"></script>-->

    <script>
        // Define custom action renderer for this page
        window.actionRenderer = function (data, type, row, meta) {
            // console.debug('Action Renderer called with:', {data, type, row, meta});
            return `
            <a class="btn btn-square btn-sm btn-primary" href="<?= route_to('edit-communication-templates', $org_slug); ?>?slug=${data.slug}&channel=${data.channel}">
                <span class="material-symbols-rounded !text-base">
                    edit_square
                </span>
            </a>
        `;
        };

        /**
         * Define column width overrides for this page
         * @type {Array<string>}
         */
        let attributes = <?= json_encode($attributes) ?>;

        /**
         *
         * @type {{[p: string]: {width?: string, data?: any, targets?: number, visible?: boolean, orderable?: boolean, searchable?: boolean, className?: string, render?: (data: any, type: string, row: any, meta: any) => string}}}
         */
        window.columnOverrides = Object.fromEntries(
            attributes.map((attr, index) => {
                if (attr === 'name') {
                    return [attr, {
                        width: '15%',
                        className: 'font-semibold text-primary',
                        render: function (data, type, row, meta) {
                            return `<a href="<?= route_to('edit-communication-templates', $org_slug); ?>?slug=${row.slug}&channel=${row.channel}" class="text-xxs md:text-xs hover:underline">${data}</a>`;
                        }
                    }];
                } else if (attr === 'description') {
                    return [attr, {width: '20%'}];
                } else if (attr === 'context') {
                    return [attr, {
                        class: 'text-xxs sm:text-xs',
                        render: function (data, type, row, meta) {
                            // We'll add badges for different contexts
                            let badgeClass = 'bg-gray-100 text-gray-800 border-gray-200';
                            if (data === 'marketing') {
                                badgeClass = 'bg-purple-100 text-purple-800 border-purple-200';
                            } else if (data === 'transactional') {
                                badgeClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                            } else if (data === 'notification') {
                                badgeClass = 'bg-indigo-100 text-indigo-800 border-indigo-200';
                            } else if (data === 'auth') {
                                badgeClass = 'bg-red-100 text-red-800 border-red-200';
                            }

                            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xxs md:text-xs font-medium border ${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                        }
                    }];
                } else if (attr === 'channel') {
                    return [attr, {
                        class: 'text-xxs sm:text-xs',
                        render: function (data, type, row, meta) {
                            // We'll add badges for different channels, it's either an email or an sms
                            let badgeClass = 'bg-gray-100 text-gray-800 border-gray-200';
                            if (data === 'email') {
                                badgeClass = 'bg-blue-100 text-blue-800 border-blue-200';
                            } else if (data === 'sms') {
                                badgeClass = 'bg-green-100 text-green-800 border-green-200';
                            }

                            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xxs md:text-xs font-medium border ${badgeClass}">${data.toUpperCase()}</span>`;
                        }
                    }];
                } else if (attr === 'customized') {
                    return [attr, {
                        class: 'text-xxs sm:text-xs',
                        render: function (data, type, row, meta) {
                            if (data === 'Yes' || data === true || data === 1) {
                                return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xxs md:text-xs font-medium bg-green-100 text-green-800 border border-green-200">Yes</span>`;
                            } else {
                                return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xxs md:text-xs font-medium bg-red-100 text-red-800 border border-red-200">No</span>`;
                            }
                        }
                    }];
                } else if (attr === 'last_modified') {
                    // We'll use Luxon for date formatting
                    return [attr, {
                        class: 'text-xxs sm:text-xs',
                        render: function (data, type, row, meta) {
                            if (data) {
                                const dt = luxon.DateTime.fromFormat(data, "yyyy-MM-dd HH:mm:ss", {zone: 'utc'}).setZone(luxon.DateTime.local().zoneName);
                                return dt.toLocaleString(luxon.DateTime.DATETIME_MED);
                            } else {
                                return 'N/A';
                            }
                        }
                    }];
                } else {
                    return [attr, {}];
                }
            })
        );
    </script>

<?= view('components/datatable_script', [
    'id' => 'companies-table',
    'attributes' => $attributes,
    'ajax_url' => route_to('get-communication-templates', $org_slug),
    'name' => 'companies',
]) ?>

<?= $this->endSection() ?>