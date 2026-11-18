# Employee leave dashboard <Badge type="warning" text="04 · Advanced" />

Build a company-scoped dashboard on top of three related tables: `leave_requests`, `employees`, and `leave_types`. The complete example can run as a **client-side snapshot** or a **server-side dashboard**; both share the same query, row mapper, Blade markup and JavaScript.

[Preview the dashboard with sample data](../examples/#try-the-dashboard) · [Copy the complete Laravel example](https://github.com/rmunate/EasyDataTable/tree/master/examples)

## 1. Protect the routes and scope the query

Apply `auth` to both data endpoints and the review endpoint. Read `company_id` from the signed-in user, never from a browser filter. Use the [SQLite fixture and setup steps](https://github.com/rmunate/EasyDataTable/tree/master/examples#employee-leave-dashboard) to try the example with companies `10` and `20`.

::: code-group
<<< @/../examples/routes.php#leave-routes [Routes]
<<< @/../examples/ServerSide/LeaveTableController.php#scoped-query [Base query]
:::

The explicit projection defines the table's public fields: JavaScript sorts `employee`, but the query searches `employees.name`. The left join preserves requests that do not yet have a leave type.

## 2. Combine external filters and global search

The browser sends `status` and `started_since` as additional parameters on each server-side draw. Validate them **before** passing the builder to EasyDataTable. The example also limits each request to at most 100 rows; its page-size menu offers 5, 10 and 25.

<<< @/../examples/ServerSide/LeaveTableController.php#server-action

Status and start date restrict the **base query** and therefore `recordsTotal`. Global search applies on top, so `recordsFiltered` can be lower. For company `10`, selecting **Pending** yields a base total of **3**. Searching for `Vacation` leaves **2**; adding **Starts on or after October 10, 2026** leaves request `104` only.

::: warning Keep OR conditions inside one group
The intended condition is `company AND status AND (employee OR code OR type OR reason)`. An `orWhere()` added directly to the base builder can bypass the company restriction.
:::

## 3. Format rows and authorize actions

<<< @/../examples/ServerSide/LeaveTableController.php#row-map

Keep dates as ISO strings for sorting. The frontend displays them in the user's locale, escapes names and reasons as text, and creates a review link only when `review_url` is present. `review_url` is a mapped field, so its column is not orderable.

::: warning A hidden action is not authorization
The review route checks the Gate **and** loads the pending request through the same company scope. A reviewer in company `10` cannot open `/examples/leave/review/201` from company `20`. The sample route returns details; a real write action also needs its own authorization and CSRF protection.
:::

## 4. Wire up the responsive frontend

The search field debounces input before drawing. Changing a status or date reloads the Ajax source and resets pagination. Responsive keeps employee and status visible longest on small screens; the error region surfaces invalid filters or a failed request.

::: code-group
<<< @/../examples/ServerSide/leave.blade.php{html} [Blade]
<<< @/../examples/table.js [JavaScript]
:::

Use the same initializer with [Metronic and an external toolbar](./metronic).

## Bonus: report by leave type

To sort aggregate counts, give them a column name in an outer query. This alternative controller action reuses `visibleRequests()` and expects a DataTables request with `leave_type` and `request_count` columns.

```php
$byType = $this->visibleRequests($request)->reorder()
    ->select('leave_types.name as leave_type')
    ->selectRaw('count(*) as request_count')
    ->groupBy('leave_types.name');

$report = $byType->newQuery()
    ->fromSub($byType, 'leave_summary')
    ->select('leave_type', 'request_count')
    ->orderByDesc('request_count');

return EasyDataTable::init()
    ->serverSide()
    ->request($request)
    ->query($report)
    ->search(fn ($query, $term) => $query->where('leave_type', 'like', "%{$term}%"))
    ->response();
```

One result row now represents a leave type, so the counts describe report rows, not individual leave requests.
