<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leave requests | EasyDataTable</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.7/css/responsive.dataTables.min.css">
    <style>
        body { max-width: 76rem; margin: 3rem auto; padding: 0 1.25rem; font: 1rem/1.5 system-ui, sans-serif; }
        .filters { display: flex; flex-wrap: wrap; gap: 1rem; margin-block: 1.5rem; }
        .filters label { display: grid; gap: .35rem; }
        .filters :is(input, select) { padding: .5rem; font: inherit; }
        .status { display: inline-block; padding: .15rem .65rem; border-radius: 2rem; background: #e9eef5; }
        .status[data-status="pending"] { background: #fff1cd; color: #794600; }
        .status[data-status="approved"] { background: #dcf4ea; color: #135c43; }
        .status[data-status="rejected"], [role="alert"] { color: #972b2b; }
        .status[data-status="rejected"] { background: #fbe4e4; }
    </style>
</head>
<body>
    <main data-leave-dashboard>
        <h1>Employee leave requests</h1>
        <p>{{ $serverSide ? 'Server-side dashboard' : 'Client-side snapshot' }} for your company.</p>
        <div class="filters">
            <label>
                Search requests
                <input type="search" data-leave-search placeholder="Try Vacation or E-100">
            </label>
            @if ($serverSide)
                <label>
                    Status
                    <select data-leave-status>
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </label>
                <label>
                    Starts on or after
                    <input type="date" data-leave-from>
                </label>
            @endif
        </div>
        <p role="alert" data-leave-error hidden></p>
        <table class="display" style="width: 100%"
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
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.7/js/dataTables.responsive.min.js"></script>
    <script type="module">
        import { mountLeaveTable } from "{{ asset('examples/leave/table.js') }}";
        mountLeaveTable(document.querySelector('[data-leave-dashboard]'));
    </script>
</body>
</html>
