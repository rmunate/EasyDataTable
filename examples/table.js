/**
 * Initialize the example table after jQuery and DataTables load.
 * The HTML table's data-server-side attribute selects the processing mode.
 */
$(function () {
    const text = $.fn.dataTable.render.text();
    const table = $('#datatable').DataTable({
        ajax: '/module/datatable',
        processing: true,
        columns: [
            ...['identification', 'employee', 'novelty_type', 'description',
                'calendar_days', 'business_days', 'initial_date', 'final_date']
                .map(data => ({ data, render: text })),
            {
                data: 'action',
                orderable: false,
                searchable: false,
                render: (action, type) => type === 'display' && action.editar
                    ? '<button type="button" class="btn-edit">Edit</button>' : ''
            }
        ]
    });

    // Optional external search field, e.g. with the Metronic theme.
    $('[data-kt-docs-table-filter="search"]').on('input', function () {
        table.search(this.value).draw();
    });
});
