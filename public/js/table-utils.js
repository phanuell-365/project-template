
window.TableUtils = window.TableUtils || {};

window.TableUtils.buildColumnDefs = function (columns, overrides = {}, options = {}) {
    // Options defaults
    const opts = Object.assign({
        prependCheckbox: true,
        appendActions: true,
        defaultWidth: '10%',
        defaultClassName: 'text-start small'
    }, options);

    const defs = [];

    // 1. Handle Checkbox (Target 0)
    if (opts.prependCheckbox) {
        defs.push({
            data: null,
            orderable: false,
            className: 'select-checkbox',
            targets: 0,
            render: () => '',
            checkboxes: {
                selectRow: true,
                selectAllRender: '<i class="far fa-check-square"></i>',
                unselectAllRender: '<i class="far fa-square"></i>',
            },
            width: '5%',
        });
    }

    // Calculate offset for targets (1 if checkbox exists, 0 otherwise)
    const targetOffset = opts.prependCheckbox ? 1 : 0;

    // 2. Build Data Columns
    // Ensure columns is an array (handle object/array mismatch from PHP)
    const colArray = Array.isArray(columns) ? columns : Object.values(columns);

    colArray.forEach((colName, index) => {
        // Default settings for every column
        const baseDef = {
            data: colName,
            targets: index + targetOffset,
            visible: true,
            searchable: true,
            orderable: true,
            className: opts.defaultClassName,
            width: opts.defaultWidth,
        };

        // Find override by Column Name (preferred) or Index
        const override = overrides[colName] || overrides[index] || {};

        // Merge base with override
        defs.push(Object.assign({}, baseDef, override));
    });

    // 3. Handle Actions (Target -1)
    if (opts.appendActions) {
        defs.push({
            data: null,
            orderable: false,
            className: 'text-center',
            targets: -1,
            render: (data, type, row) => `
                <a class="btn btn-datatable btn-icon btn-transparent-dark" href="#!"><i data-feather="trash-2"></i></a>
            `,
            width: '5%',
        });
    }

    return defs;
};
