---
description: Reuse the leave dashboard initializer inside a Metronic card with external search and filters.
---

# Metronic integration <Badge type="warning" text="05 · Integration" />

Run the [employee leave dashboard](./employee-leave.md) first. Once both Laravel endpoints work, move its view into a Metronic page without rewriting the backend or DataTables configuration.

## 1. Confirm the bundle loads all four dependencies

Use a Metronic bundle that includes **jQuery, DataTables 2, the Responsive extension and `KTUtil`**. Asset paths depend on your Metronic distribution. Load its CSS and JavaScript before the dashboard initializer; remove the standalone DataTables CDN tags and any Metronic-generated initializer for this table to avoid loading or mounting it twice.

Run this in the browser console on the Metronic page:

```js
console.table({
    jquery: typeof window.jQuery,
    dataTables: window.DataTable?.version,
    responsive: typeof window.DataTable?.Responsive,
    ready: typeof window.KTUtil?.onDOMContentLoaded
});
```

Expect DataTables `2.x` and `function` for the other three entries. If `dataTables` shows `1.x` or a dependency is missing, update the bundle or keep the standalone view; the example initializer targets DataTables 2.

## 2. Wrap the table and filters in a card

In the Metronic Blade view served by the leave routes, replace the standalone `<main>` block with the card below. Keep the `serverSide` variable set by `Route::view()`. If you want colored status badges, keep the standalone view's `.status` styles in the app stylesheet.

```html [Blade card]
<div class="card" data-leave-dashboard>
    <div class="card-header">
        <h1 class="card-title">Employee leave requests</h1>
        <div class="card-toolbar gap-3 flex-wrap">
            <label>
                Search requests
                <input type="search" class="form-control" data-leave-search
                       placeholder="Search by name or type">
            </label>
            @if ($serverSide)
                <label>
                    Status
                    <select class="form-select" data-leave-status>
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </label>
                <label>
                    Starts on or after
                    <input type="date" class="form-control" data-leave-from>
                </label>
            @endif
        </div>
    </div>
    <div class="card-body">
        <p class="text-danger" role="alert" data-leave-error hidden></p>
        <table class="table table-row-bordered w-100"
               data-source="{{ route($serverSide ? 'examples.leave.server-data' : 'examples.leave.client-data') }}"
               data-server-side="{{ $serverSide ? 'true' : 'false' }}">
            <thead>
                <tr>
                    <th>Request</th><th>Code</th><th>Employee</th><th>Type</th>
                    <th>Reason</th><th>Starts on</th><th>Status</th><th>Review</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
```

Keep `data-leave-search`, `data-leave-error` and the table inside the same `[data-leave-dashboard]` container. The server-side view also needs both filter inputs. The header order matches the `columns` array in `table.js`.

## 3. Mount after Metronic loads

Keep [`table.js`](https://github.com/rmunate/EasyDataTable/blob/master/examples/table.js) at `public/examples/leave/table.js`. Place this module after the Metronic asset scripts, using your layout's script stack if it has one. This example mounts one dashboard:

```html
<script type="module">
    import { mountLeaveTable } from "{{ asset('examples/leave/table.js') }}";

    window.KTUtil.onDOMContentLoaded(() => {
        mountLeaveTable(document.querySelector('[data-leave-dashboard]'));
    });
</script>
```

## 4. Verify the integration

1. Open `/examples/leave/server` while signed in. Check that the first Ajax request goes to `/examples/leave/server-data` and returns JSON.
2. Type `Vacation` in the toolbar and change **Status** to **Pending**. Confirm that a draw is sent with `search[value]` and `status` and that the table resets to page one when the filter changes.
3. Open `/examples/leave/client`. It should load `client-data` and hide both server-only filters. Typing in the toolbar should search without a new Ajax request.
4. Narrow the browser window and confirm you can still read the employee and status, and expand a row to see its other columns.

To add a refresh button later, save the instance returned by `mountLeaveTable()` and call `table.ajax.reload(null, false)` in the button's click handler to keep the current page.
