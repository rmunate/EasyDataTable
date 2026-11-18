/**
 * Mount the dashboard after jQuery, DataTables 2 and its Responsive extension load.
 * The table's data attributes select the endpoint and processing mode.
 */
export function mountLeaveTable(dashboard) {
    const DataTable = window.DataTable;
    const $table = window.jQuery(dashboard.querySelector('table'));
    const search = dashboard.querySelector('[data-leave-search]');
    const status = dashboard.querySelector('[data-leave-status]');
    const from = dashboard.querySelector('[data-leave-from]');
    const error = dashboard.querySelector('[data-leave-error]');
    const serverSide = $table.attr('data-server-side') === 'true';
    const text = DataTable.render.text();
    const date = new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeZone: 'UTC' });

    $table.on('xhr.dt', (_event, _settings, json, xhr) => {
        error.hidden = json !== null;
        if (json === null) {
            error.textContent = xhr?.status === 422
                ? 'The filters were rejected. Check the requested date, status, and page size.'
                : 'Could not load leave requests. Check your connection and sign-in.';
            return true;
        }
    });

    const table = $table.DataTable({
        serverSide,
        processing: true,
        responsive: true,
        orderMulti: false,
        order: [[0, 'desc']],
        pageLength: 5,
        lengthMenu: [5, 10, 25],
        layout: { topEnd: null },
        ajax: {
            url: $table.attr('data-source'),
            headers: { Accept: 'application/json' },
            data(params) {
                if (serverSide && status.value) params.status = status.value;
                if (serverSide && from.value) params.started_since = from.value;
            }
        },
        columns: [
            { data: 'request_id', responsivePriority: 3 },
            { data: 'employee_code', render: text },
            { data: 'employee', render: text, responsivePriority: 1 },
            { data: 'leave_type', render: text },
            { data: 'reason', render: text },
            {
                data: 'starts_on',
                render: (value, type) => type === 'display' ? date.format(new Date(`${value}T00:00:00Z`)) : value
            },
            {
                data: 'status',
                responsivePriority: 2,
                render(value, type) {
                    if (type !== 'display') return value;
                    const badge = document.createElement('span');
                    badge.className = 'status';
                    badge.dataset.status = value;
                    badge.textContent = value[0].toUpperCase() + value.slice(1);
                    return badge;
                }
            },
            {
                data: 'review_url',
                orderable: false,
                searchable: false,
                render(url, type) {
                    if (type !== 'display' || !url) return '';
                    const link = document.createElement('a');
                    link.href = url;
                    link.textContent = 'Review';
                    return link;
                }
            }
        ]
    });

    search.addEventListener('input', DataTable.util.debounce(() => table.search(search.value).draw(), 300));
    if (serverSide) {
        status.addEventListener('change', () => table.ajax.reload());
        from.addEventListener('change', () => table.ajax.reload());
    }

    return table;
}
