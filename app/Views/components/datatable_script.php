<?php
/**
 * @var string $id
 * @var array $attributes
 * @var string $ajax_url
 * @var string $name
 */
?>
<script>
    $(document).ready(() => {
        const tableId = '<?= $id ?>';
        let attributes = <?= json_encode($attributes) ?>;
        const ajaxUrl = '<?= $ajax_url ?>';

        // Lodash conversion if needed
        if (!Array.isArray(attributes)) {
            attributes = _.toArray(attributes);
        }

        // 1. Get overrides from global scope if defined by the parent view
        const overrides = window.columnOverrides || {};

        console.debug('Column Overrides:', overrides);

        // 2. Get action renderer from global scope or use default
        const actionRenderer = window.actionRenderer || function (data, type, row) {
            return `
                <div class="flex justify-center items-center space-x-2">
                    <a class="text-blue-500" href="#" aria-label="Edit"><span class="material-symbols-rounded">edit</span></a>
                </div>
            `;
        };

        // 3. Build Column Definitions
        const columnDefs = attributes.map((columnName, index) => {
            let def = {
                "data": columnName,
                "targets": index + 1,
                "visible": true,
                "searchable": true,
                "orderable": true,
                "className": "text-start text-xxs sm:text-xs ",
                "width": "10%",
            };

            // Apply overrides if they exist for this column
            if (overrides[columnName]) {
                def = {...def, ...overrides[columnName]};
            }

            return def;
        });

        const tableEl = $(`#${tableId}`);

        // 4. Initialize DataTable
        tableEl.DataTable({
            "paging": true,
            "columnDefs": [
                {
                    // Checkbox column
                    data: null,
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0,
                    render: function (data, type, row, meta) {
                        const rowId = row.id || data.id;
                        return `
                        <div class="flex items-center justify-center">
                            <label for="check_${rowId}" class="sr-only">Select row</label>
                            <input type="checkbox" name="check_${rowId}" id="check_${rowId}" class="checkbox checkbox-xs sm:checkbox-sm checkbox-primary">
                        </div>
                        `
                    },
                    // 'checkboxes': {
                    //     'selectRow': true,
                    //     'selectAllRender': '<span class="material-symbols-rounded">check_box</span>',
                    //     'unselectAllRender': '<span class="material-symbols-rounded">check_box_outline_blank</span>',
                    // },
                    'width': '5%',
                },
                {
                    // Actions column
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    targets: -1,
                    render: actionRenderer,
                    'width': '5%',
                },
                ...columnDefs,
            ],
            "rowCallback": function (row, data, index) {
                $(row).addClass('hover')
            },
            "searching": {
                "regex": true,
                "smart": true
            },
            "info": true,
            "select": {
                "style": "multi",
                "selector": "td:first-child"
            },
            "language": {
                "lengthMenu": "_MENU_",
                // "zeroRecords": "No records found",
                "zeroRecords": `
                <div class="flex flex-col items-center justify-center py-8">
                    <span class="material-symbols-rounded text-gray-400 !text-6xl mb-4">inbox</span>
                    <div class="text-gray-500 text-sm lg:text-base">No records found</div>
                </div>
                `,
                // "info": "Showing page _PAGE_ of _PAGES_",
                "info": `
                <div class="text-xs lg:text-sm">
                    Showing <span class="font-semibold text-primary">_START_</span> to <span class="font-semibold text-primary">_END_</span> of <span class="font-semibold text-primary">_TOTAL_</span> entries
                </div>
                `,
                "infoEmpty": "No records available",
                // "infoFiltered": "(filtered from _MAX_ total records)",
                "infoFiltered": `
                <div class="text-xs lg:text-sm">
                    (filtered from <span class="font-semibold text-primary">_MAX_</span> total records)
                </div>
                `,
                "search": "Search:",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "ajax": {
                "url": ajaxUrl,
                "type": "GET",
                "dataSrc": function (json) {
                    return json.data;
                }
            },
            "buttons": [
                {
                    "extend": 'copy',
                    "text": '<div class="inline-flex gap-x-2 items-center justify-center"><span class="material-symbols-rounded !text-xl">content_copy</span> <span class="hidden lg:block">Copy</span></div>'
                },
                {
                    "extend": 'csv',
                    "text": '<div class="inline-flex gap-x-2 items-center justify-center"><span class="material-symbols-rounded !text-xl">csv</span>  <span class="hidden lg:block">CSV</span></div>'
                },
                {
                    "extend": 'excel',
                    "text": '<div class="inline-flex gap-x-2 items-center justify-center"><span class="material-symbols-rounded !text-xl">table</span>  <span class="hidden lg:block">Excel</span></div>'
                },
                {
                    "extend": 'pdf',
                    "text": '<div class="inline-flex gap-x-2 items-center justify-center"><span class="material-symbols-rounded !text-xl">picture_as_pdf</span>  <span class="hidden lg:block">PDF</span></div>'
                },
                {
                    "extend": 'print',
                    "text": '<div class="inline-flex gap-x-2 items-center justify-center"><span class="material-symbols-rounded !text-xl">print</span>  <span class="hidden lg:block">Print</span></div>'
                },
            ],
            "dom": `<"flex flex-1 flex-col md:flex-row justify-start sm:justify-center items-center mb-4 gap-3 md:!space-y-0"<"flex items-center basis-2/12 w-full"l><"basis-7/12 flex flex-row items-center justify-center w-full"B>f>r<"mt-4 overflow-auto card border"<"card-body"t>><"flex flex-col sm:flex-row justify-center md:justify-between items-center space-y-5 sm:space-y-0 mt-5 md:mt-8"<"inline-flex items-center justify-end self-start text-xs lg:text-sm font-semibold"i><"inline-flex items-center justify-end self-end"p>>`,
            "drawCallback": function (settings) {
                const paginationButtons = $('.paginate_button');
                // paginationButtons.addClass('btn join-item');
                const currentButton = $('.paginate_button.current');
                currentButton.attr('class', '').addClass('btn btn-xs sm:btn-sm md:btn-md join-item btn-primary');

                // Check if the paginate_button's aria-disabled is true and disable the button
                paginationButtons.each(function () {
                    const isDisabled = $(this).attr('aria-disabled') === 'true';
                    if (isDisabled) {
                        $(this).addClass('btn btn-sm md:btn-md btn-disabled join-item');
                        // Add disabled attribute to the button
                        $(this).attr('disabled', 'disabled');
                    } else {
                        $(this).addClass('btn btn-sm md:btn-md btn-primary join-item');
                        // Remove disabled attribute from the button
                        $(this).removeAttr('disabled');
                    }
                });
            },
            "headerCallback": function (thead, data, start, end, display) {
                // We'll capture the "th"s and add a sort icons depending on the aria-sort attribute
                $(thead).find('th').each(function () {
                    //const selectAllCheckbox = $(this).find('#<?php //= $id ?>//-select-all-column');
                    if ($(this).is('#<?= $id ?>-select-all-column') || $(this).is('#<?= $id ?>-actions-column')) {
                        // Skip the select all checkbox column
                        return;
                    }

                    // Add flex and place items center to the th

                    const ariaSort = $(this).attr('aria-sort');
                    // Remove existing sort icons
                    $(this).find('.sort-icon').remove();

                    if (ariaSort === 'ascending') {
                        $(this).append('<span class="material-symbols-rounded sort-icon !text-base ml-2">arrow_upward</span>');
                    } else if (ariaSort === 'descending') {
                        $(this).append('<span class="material-symbols-rounded sort-icon !text-base ml-2">arrow_downward</span>');
                    } else {
                        // No sort
                        $(this).append('<span class="material-symbols-rounded sort-icon !text-base text-gray-400 ml-2">unfold_more</span>');
                    }

                    // Set the aria-label's value as title attribute for better accessibility
                    const ariaLabel = $(this).attr('aria-label');
                    if (ariaLabel) {
                        $(this).attr('title', ariaLabel);
                    }
                });
            },
            // "footerCallback": function (tfoot, data, start, end, display) {
            //     // Create search input for each footer cell
            //     $(tfoot).find('th').each(function (index) {
            //         if (index === 0 || index === attributes.length + 1) {
            //             // Skip the checkbox and actions columns
            //             $(this).html('');
            //             return;
            //         } else {
            //             const title = $(this).text();
            //             $(this).html(`
            //                 <input type="text" class="input input-xs sm:input-sm md:input-md input-bordered input-primary w-full" placeholder="Search ${title}" />
            //             `);
            //
            //             // Apply search on input change
            //             $(this).find('input').on('keyup change clear', function () {
            //                 if (tableEl.DataTable().column(index).search() !== this.value) {
            //                     tableEl.DataTable().column(index).search(this.value).draw();
            //                 }
            //             });
            //         }
            //     });
            // },
            "initComplete": function () {
                // Initialize the state of the "select all" checkbox
                updateSelectAllCheckbox();

                //     // Create search input for each footer cell
                this.api()
                    .columns()
                    .every(function (index) {
                        const column = this;
                        const th = $(column.footer());
                        if (index === 0 || index === attributes.length + 1) {
                            // Skip the checkbox and actions columns
                            th.html('');

                        } else {
                            const title = th.text().trim();
                            th.html(`
                                <input type="text" class="input input-xs sm:input-sm md:input-md input-bordered input-primary w-full font-normal" placeholder="${title}" />
                            `);

                            // Apply search on input change
                            th.find('input').on('keyup change clear', function () {
                                if (column.search() !== this.value) {
                                    column.search(this.value).draw();
                                }
                            });
                        }
                    });

            }
        });

        // We need to listen for select, deselect events to manage the checkboxes
        tableEl.on('select.dt deselect.dt', function () {
            const selectedRows = tableEl.DataTable().rows({selected: true}).indexes();
            // First, uncheck all checkboxes
            tableEl.find('input[type="checkbox"]').prop('checked', false);
            // Then, check the selected rows
            selectedRows.each(function (index) {
                const rowNode = tableEl.DataTable().row(index).node();
                $(rowNode).find('input[type="checkbox"]').prop('checked', true);
            });

            // Update the state of the "select all" checkbox
            updateSelectAllCheckbox();
        });

        // Handle click on checkbox to select/deselect row
        tableEl.on('click', 'input[type="checkbox"]', function (e) {
            const row = tableEl.DataTable().row($(this).closest('tr'));
            if (this.checked) {
                row.select();
            } else {
                row.deselect();
            }
            e.stopPropagation();

            // Update the state of the "select all" checkbox
            updateSelectAllCheckbox();
        });

        function updateSelectAllCheckbox() {
            const allRows = tableEl.DataTable().rows().indexes();
            const selectedRows = tableEl.DataTable().rows({selected: true}).indexes();
            const selectAllCheckboxTd = $('#<?= $id ?>-select-all-column');
            const selectAllCheckbox = selectAllCheckboxTd.find('input[type="checkbox"]');

            if (selectedRows.length === 0) {
                selectAllCheckbox.prop('indeterminate', false);
                selectAllCheckbox.prop('checked', false);
            } else if (selectedRows.length === allRows.length) {
                selectAllCheckbox.prop('indeterminate', false);
                selectAllCheckbox.prop('checked', true);
            } else {
                selectAllCheckbox.prop('indeterminate', true);
            }
        }

        // Handle click on "select all" control
        $('#<?= $id ?>-select-all-column').on('click', 'input[type="checkbox"]', function (e) {
            if (this.checked) {
                tableEl.DataTable().rows().select();
            } else {
                tableEl.DataTable().rows().deselect();
            }
            e.stopPropagation();
        });


        // Listen for the filter button click
        $('#filter-<?= $id ?>-button').on('click', function () {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            // Apply the date range filter to the DataTable
            tableEl.DataTable().ajax.url(ajaxUrl + `?date_from=${startDate}&date_to=${endDate}`).load();
        });

        // Listen for the reset button click
        $('#reset-<?= $id ?>-button').on('click', function () {
            // Clear the date inputs
            // $('#start_date').val('');
            // $('#end_date').val('');
            // Set the range to be between today and 2 days ago since that's the default filter
            // We can use Luxon for date manipulation
            // const DateTime = luxon.DateTime;
            // const today = DateTime.now().toFormat('dd/MM/yyyy');
            // const twoDaysAgo = DateTime.now().minus({days: 2}).toFormat('dd/MM/yyyy');
            //
            // $('#start_date').val(twoDaysAgo);
            // $('#end_date').val(today);
            //
            // // Reset the DataTable ajax URL to the original
            // tableEl.DataTable().ajax.url(ajaxUrl).load();

            // Way too complicated, just reload the page
            location.reload();
        });
    })
</script>

<script>
    $(document).ready(() => {
        const tableLengthDropdownContainer = $("#<?= $name ?>-table_length");
        tableLengthDropdownContainer.addClass('w-full')
        const selectLabel = $(tableLengthDropdownContainer).find('label');
        selectLabel.addClass('form-control w-full text-xs')
        const selectElement = $(tableLengthDropdownContainer).find('select');
        selectElement.addClass('select select-sm md:select-md select-primary select-bordered w-full');

        console.log('tableLengthDropdownContainer', tableLengthDropdownContainer)
    })
</script>

<script>
    $(document).ready(() => {
        // select the filter wrapper
        const tableFilterContainer = $("#<?= $name ?>-table_filter");
        tableFilterContainer.addClass('relative z-0 basis-3/12')
        tableFilterContainer.append(`
        <div class="absolute end-2 md:end-4 top-0.5 md:top-3 z-10">
            <span class="material-symbols-rounded text-primary !text-base md:!text-2xl">search</span>
        </div>`
        );
        // get the label element
        const searchLabel = $(tableFilterContainer).find('label');
        // searchLabel.addClass('absolute tracking-[.03125em] text-gray-500 bg-neutral-10 duration-300 transform px-1 -translate-y-6 scale-75 top-3 z-10 origin-[0] start-12 peer-focus:start-12 peer-focus:text-soko-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 peer-focus:bg-neutral-10 peer-focus:px-1 peer-invalid:text-error-600')
        searchLabel.addClass('sr-only')
        searchLabel.attr('for', '<?= $name ?>-table_filter_input');
        const searchElement = $(tableFilterContainer).find('input');
        // searchLabel.addClass('relative flex flex-row items-center justify-center space-x-2 text-gray-400 focus-within:text-gray-600');
        searchElement.addClass('input input-xs sm:input-sm md:input-md input-bordered input-primary w-full')
        searchElement.attr('id', '<?= $name ?>-table_filter_input');
        searchElement.attr('placeholder', 'Search');
        searchElement.attr('aria-label', '<?= $name ?>-table_filter_input');

        // add the searchElement to the label
        // searchLabel.append(searchElement);
        tableFilterContainer.append(searchElement);


        const dtButtons = $('.dt-buttons');

        dtButtons.addClass('join join-horizontal');

        const dtButton = $('.dt-button');

        dtButton.addClass('btn btn-sm md:btn-md btn-primary btn-outline join-item');

        // pagination

        const tablePaginationContainer = $("#<?= $name ?>-table_paginate");

        tablePaginationContainer.addClass('join join-horizontal');

        const currentButton = $(tablePaginationContainer).find('.paginate_button.current');

        // console.debug('currentButton', currentButton);

        // Check if the paginate_button's aria-disabled is true and disable the button
        tablePaginationContainer.find('.paginate_button').each(function () {
            const isDisabled = $(this).attr('aria-disabled') === 'true';
            if (isDisabled) {
                $(this).addClass('btn btn-sm md:btn-md btn-disabled join-item');
            } else {
                $(this).addClass('btn btn-sm md:btn-md btn-primary join-item');
            }
        });

        currentButton.addClass('btn btn-sm md:btn-md btn-primary join-item');

        const rangePicker = $('#range-picker');
        new DateRangePicker(rangePicker[0], {
            format: 'dd/mm/yyyy',
        });

    })
</script>


<script>
    // 2. Create a shadow root and attach Flowbite inside it
    //const container = document.getElementById('flowbite-datepicker-container');
    //const shadow = container.attachShadow({mode: 'open'});
    //
    //// Add our tailwind CSS to the shadow DOM as well
    //const tailwindLink = document.createElement('link');
    //tailwindLink.rel = 'stylesheet';
    //tailwindLink.href = <?php //= "'" . base_url('css/tailwind.css') . "'"; ?>//;
    //shadow.appendChild(tailwindLink);
    //
    //const flowbiteMinScript = document.createElement('script');
    //// script.src = 'https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js';
    //flowbiteMinScript.src = <?php //= "'" . base_url('js/flowbite.min.js') . "'"; ?>//;
    //shadow.appendChild(flowbiteMinScript);
    //
    //// Add the date range picker script
    //const datePickerScript = document.createElement('script');
    //// rangeScript.src = 'https://cdn.jsdelivr.net/npm/flowbite-datepicker@1.3.4/dist/datepicker.min.js';
    //datePickerScript.src = <?php //= "'" . base_url('js/datepicker.js') . "'"; ?>//;
    //shadow.appendChild(datePickerScript);

    // const dateRangePickerScript = document.createElement('script');
    // rangeScript.src = 'https://cdn.jsdelivr.net/npm/flowbite-datepicker@1.3.4/dist/datepicker.min.js';
    //dateRangePickerScript.src = <?php //= "'" . base_url('js/datepicker-range-range.js') . "'"; ?>//;
    // shadow.appendChild(dateRangePickerScript);

    //const datePickerFullScript = document.createElement('script');
    //// rangeScript.src = 'https://cdn.jsdelivr.net/npm/flowbite-datepicker@1.3.4/dist/datepicker-full.min.js';
    //datePickerFullScript.src = <?php //= "'" . base_url('js/datepicker-full.js') . "'"; ?>//;
    //shadow.appendChild(datePickerFullScript);

    <!--    --><?php
    //    $today = date('d/m/Y');
    //
    //    $start_date_input = view('components/form-date-picker', [
    //            'props' => [
    //                    'id'                  => 'start_date',
    //                    'label'               => 'Start Date',
    //                    'name'                => 'start_date',
    //                    'type'                => 'start_date',
    //                    'value'               => $today,
    //                    'datepicker'          => false,
    //                    'datepicker-autohide' => false,
    //                    'datepicker-title'    => 'Start Date',
    //                    'has-range'           => true,
    //            ]
    //    ]);
    //
    //    $end_date_input = view('components/form-date-picker', [
    //            'props' => [
    //                    'id'                  => 'end_date',
    //                    'label'               => 'End Date',
    //                    'name'                => 'end_date',
    //                    'type'                => 'end_date',
    //                    'value'               => $today,
    //                    'datepicker'          => false,
    //                    'datepicker-autohide' => false,
    //                    'datepicker-title'    => 'End Date',
    //                    'has-range'           => true,
    //            ]
    //    ]);
    //    ?>

    // 4. Once JS loads, add the datepicker HTML to the shadow DOM
    // flowbiteMinScript.onload = function () {
    //       const datepickerHTML = `
    //   <div class="relative max-w-sm">
    //     <input datepicker type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Select date">
    //   </div>
    // `;

    // const datepickerHTML = `
    //     <div class="grid grid-cols-1 md:grid-cols-2 gap-4"  id="range-picker" date-rangepicker>
    //                    <?php //= str_replace("\n", '', $start_date_input) ?>
    //                    <?php //= str_replace("\n", '', $end_date_input) ?>
    //             </div>
    //         `;

    //     shadow.innerHTML += datepickerHTML;
    //     // Re-initialize components within the shadow root
    //     if (window.initFlowbite) {
    //         window.initFlowbite();
    //
    //         console.debug('Flowbite datepicker initialized in shadow DOM');
    //
    //         const startDate = $('#start_date', shadow);
    //         const endDate = $('#end_date', shadow);
    //
    //         new Datepicker(startDate[0], {
    //             format: 'dd/mm/yyyy',
    //             autohide: true,
    //             todayHighlight: true,
    //         });
    //
    //         new Datepicker(endDate[0], {
    //             format: 'dd/mm/yyyy',
    //             autohide: true,
    //             todayHighlight: true,
    //         });
    //
    //     }
    // };
    //
    // datePickerFullScript.onload = function () {
    //     const rangePicker = $('#range-picker', shadow);
    //     new DateRangePicker(rangePicker[0], {
    //         format: 'dd/mm/yyyy',
    //     });
    // }
</script>
