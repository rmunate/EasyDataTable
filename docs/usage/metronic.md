# Metronic integration <Badge type="warning" text="05 · Integration" />

The [employee leave dashboard](./employee-leave) can live in a Metronic card without duplicating the DataTables configuration. This adapter targets Metronic bundles with **jQuery, DataTables 2, the Responsive extension and `KTUtil`**. Use the bundle instead of the example CDN scripts, and remove any previous initializer for this table.

## Replace the toolbar, not the table

Keep the example table, its `data-source` and `data-server-side` attributes, and the error region. Wrap the toolbar and table in the same `[data-leave-dashboard]` element.

```html
<div class="card-header">
    <div class="card-toolbar gap-3">
        <input type="search" class="form-control"
               data-leave-search
               aria-label="Search leave requests" placeholder="Search by name or type">
        <select class="form-select" data-leave-status aria-label="Status">
            <option value="">All statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
        <input type="date" class="form-control" data-leave-from aria-label="Starts on or after">
    </div>
</div>
```

## Mount after Metronic loads

Keep [`examples/table.js`](https://github.com/rmunate/EasyDataTable/blob/master/examples/table.js) in `public/examples/leave/table.js`. Replace the standalone view's initializer with:

```html
<script type="module">
    import { mountLeaveTable } from "{{ asset('examples/leave/table.js') }}";

    window.KTUtil.onDOMContentLoaded(() => {
        mountLeaveTable(document.querySelector('[data-leave-dashboard]'));
    });
</script>
```

The same API supports a refresh button: save the instance returned by `mountLeaveTable()` and call `table.ajax.reload(null, false)` to reload without leaving the current page. For the client-side snapshot, omit the two server-only filters and keep `data-server-side="false"`.
