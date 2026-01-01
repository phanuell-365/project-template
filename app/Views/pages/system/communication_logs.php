<?php

/**
 * @var array $attributes
 * @var string $org_slug
 * @var array $date_filters
 */


$this->extend('layouts/main');
?>

<?= $this->section('scripts') ?>
<?= view('components/links') ?>
<?= $this->endSection() ?>

<?php $this->section('content'); ?>
<?= view('components/datatable', [
        'id'         => 'logs-table',
        'attributes' => $attributes,
        'datepicker'   => true,
        'date_filters' => $date_filters,
]) ?>
<?= $this->endSection() ?>

<?= $this->section('bottom-scripts') ?>

<script>

    window.actionRenderer = function (data, type, row, meta) {
        // console.debug('Action Renderer called with:', {data, type, row, meta});
        return `
            <a class="btn btn-square btn-sm btn-primary" href="<?= route_to('communication-log-details-view', $org_slug); ?>?log_id=${data.id}">
                <span class="material-symbols-rounded !text-base">
                    visibility
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
            if (attr === 'subject') {
                return [attr, {
                    width: '25%',
                    className: 'font-semibold text-primary text-xxs sm:text-xs',
                    render: function (data, type, row, meta) {
                        //return `<a href="<?php //= route_to('communication-log-details-view', $org_slug); ?>//?log_id=${row.id}" class="text-xxs md:text-xs hover:underline">${data}</a>`;
                        // Since an sms will always have an empty subject, get the first 20 characters of the body instead when row.channel is 'sms'
                        if (row.channel === 'sms') {
                            let sms_subject = row.body ? row.body.substring(0, 20) : 'N/A';
                            if (row.body && row.body.length > 20) {
                                sms_subject += '...';
                            }
                            return `<a href="<?= route_to('communication-log-details-view', $org_slug); ?>?log_id=${row.id}" class="text-xxs sm:text-xs hover:underline">${sms_subject}</a>`;
                        } else {
                            return `<a href="<?= route_to('communication-log-details-view', $org_slug); ?>?log_id=${row.id}" class="text-xxs sm:text-xs hover:underline">${data}</a>`;
                        }
                    }
                }];
            } else if (attr === 'status') {
                return [attr, {
                    width: '10%',
                    className: 'text-center text-xxs sm:text-xs',
                    render: function (data, type, row, meta) {
                        // let statusClass = data === 'Sent' ? 'text-green-600 font-semibold' :
                        //     data === 'Failed' ? 'text-red-600 font-semibold' :
                        //         'text-gray-600 font-semibold';
                        // return `<span class="${statusClass}">${data}</span>`;
                        let badgeClass = 'bg-green-100 text-green-800 border-green-200';

                        if (data === 'pending') {
                            badgeClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                        } else if (data === 'failed') {
                            badgeClass = 'bg-red-100 text-red-800 border-red-200';
                        }

                        return `<span class="inline-flex items-center px-2 py-0.5 rounded text-xxs font-medium border ${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                    }
                }];
            } else if (attr === 'channel') {
                return [attr, {
                    width: '10%',
                    className: 'text-center text-xxs sm:text-xs',
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
            } else if (attr === 'sent_at') {
                return [attr, {
                    width: '15%',
                    className: 'text-center text-xxs sm:text-xs',
                    render: function (data, type, row, meta) {
                        if (data) {
                            const dt = luxon.DateTime.fromFormat(data, "yyyy-MM-dd HH:mm:ss", {zone: 'utc'}).setZone(luxon.DateTime.local().zoneName);
                            return dt.toLocaleString(luxon.DateTime.DATETIME_MED);
                        } else {
                            return 'N/A';
                        }
                    }
                }];
            } else if (attr === 'recipient_email' || attr === 'recipient_phone') {
                return [attr, {
                    width: '20%',
                    className: 'text-center text-xxs sm:text-xs',
                    // they might be empty strings, so render N/A if so
                    render: function (data, type, row, meta) {
                        return data && data.trim() !== '' ? data : '<span class="text-gray-500 italic">N/A</span>';
                    }
                }];
            }

            return [attr, {}];
        }).concat([['action', {
            width: '10%',
            targets: -1,
            orderable: false,
            searchable: false,
            className: 'text-center text-xxs sm:text-xs',
            render: window.actionRenderer
        }]])
    );
</script>

<?= view('components/datatable_script', [
        'id'         => 'logs-table',
        'attributes' => $attributes,
        'ajax_url'   => route_to('communication-logs-view', $org_slug),
        'name'       => 'logs',
]) ?>

<?php $this->endSection(); ?>
