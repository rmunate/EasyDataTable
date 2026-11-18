---
title: PHP API and JSON contract
description: All EasyDataTable configuration methods, supported sorting and search input, and JSON response shapes.
---

# PHP API & JSON contract

Use `Rmunate\EasyDatatable\EasyDataTable`. Start a new instance for each endpoint call, choose a source and processing mode, then return the array produced by `response()`.

## Fluent methods

| Method | When to use it |
| --- | --- |
| `EasyDataTable::init()` | Creates a new instance with the package's PHP runtime defaults. |
| `fromData(iterable $data)` | Selects client-side mode with an array, collection or other iterable of rows. Replaces any query. |
| `clientSide()` | Selects client-side mode for a query. Fetches the whole result when you call `response()`. |
| `serverSide()` | Selects SQL-backed global search, ordering and paging. Requires both `query()` and `request()`. |
| `query(Builder $query)` | Sets an unexecuted `Illuminate\Database\Query\Builder` and replaces any prepared rows. Does not select a mode. |
| `request(Request $request)` | Sets the `Illuminate\Http\Request` carrying a DataTables draw. Server-side only. |
| `map(Closure $map)` | Transforms each returned row. Receives the row as an object; return a value that the frontend can read, typically an associative array of column keys. |
| `search(Closure $search)` | Replaces global server-side search. Receives `(Builder $query, string $term)` and must return a Query Builder. |
| `response(): array` | Runs the chosen mode and returns the payload. In a Laravel route you can return it directly or pass it to `response()->json($payload)` to customize the HTTP response. |

Pass `DB::table(...)->select(...)` to `query()` without calling `get()`. For an Eloquent query, use an explicit projection and call `toBase()` to obtain the underlying Query Builder; if you already have results, pass them to `fromData()`.

### PHP runtime limits

Construction calls `maxExecutionTime(60)` and `memoryLimit('512M')`. To override them, chain `maxExecutionTime(int $seconds)` or `memoryLimit(string $limit)` before calling `response()`. They change PHP settings via `ini_set()` for the current script, not just for the EasyDataTable instance. They do not replace bounded requests or respect a page-size policy on your behalf.

## Client-side response

Use `fromData($rows)` or `clientSide()->query($query)`. Do not pass `request()` or `search()`. Laravel sends the entire visible result, reindexed as a JSON list even if the input uses nonconsecutive keys. An empty result is `{"data":[]}`:

```json
{
  "data": [
    { "name": "Desk lamp", "stock": 12 },
    { "name": "USB-C adapter", "stock": 0 }
  ]
}
```

In the browser, leave `serverSide` off and use `columns: [{ data: 'name' }, { data: 'stock' }]` for this payload. DataTables reads the top-level `data` key unless you configure `ajax.dataSrc` differently.

## Server-side request

Pass the Laravel request from a DataTables instance configured with `serverSide: true`. The [server-side catalog](../usage/server-side.md#inspect-a-draw) includes a complete URL you can send without DataTables.

| Input | Required? | Behavior |
| --- | --- | --- |
| `draw` | Yes | Integer `>= 0`; returned to the frontend as an integer. |
| `start` | Yes | Integer `>= 0`; zero-based offset into the filtered result. |
| `length` | Yes | Integer `>= -1`; `-1` requests all remaining rows, `0` requests none. Set your own allowed range on the route. |
| `search[value]` | No | Global term. An empty value does not filter; `0` is a search term. |
| `order[0][column]` | No | Nonnegative index into `columns`. Only the first sort instruction is used. |
| `order[0][dir]` | No | `asc` (default) or `desc`, case-insensitive. |
| `columns[index][data]` | When ordering | Exact selected field name or alias associated with the requested index. |

Integer form values such as `"10"` are accepted. Flat `search` and single `order[column]` / `order[dir]` are also supported. Per-column search, regex search and multi-column ordering are not implemented. The methods `draw()`, `start()`, `limit()`, `inputSearch()`, `order()` and `direction()` read these values from the configured request.

### How SQL search and order are resolved

- **Default search:** the package applies a grouped `LIKE '%term%'` across resolvable selected columns, using underlying column names for aliases. SQL wildcard and case-matching behavior depends on your database and collation. `columns[].searchable` does not limit this SQL search; use `search()` to restrict it.
- **Custom search:** the callback only runs for a nonempty term. Add conditions to the supplied builder, keep any `OR` clauses grouped together, and return the builder. Do not lose the authorization or other filters in the base query.
- **Ordering:** a selected name such as `stock` or a simple alias such as `employees.name as employee` can be ordered. The requested order replaces the query's existing order; if none is requested, the original order is used. Use unique aliases for joined columns.
- **Projection limits:** for a single plain table, `*` can be expanded from its schema. With joins, aliases on a table, or a subquery, select named fields explicitly. Fields produced by `map()` or raw expressions in the projection are not resolved as sortable columns. For aggregates that need ordering, [select the named aggregate from an outer query](../usage/employee-leave.md#bonus-report-by-leave-type).

## Server-side response

```json
{
  "draw": 2,
  "recordsTotal": 3,
  "recordsFiltered": 1,
  "data": [{ "name": "Desk lamp", "stock": 12 }]
}
```

1. The package works from a clone of the supplied builder and counts the rows in the base query. `recordsTotal` already includes your authorization scope and application filters.
2. It applies global search and counts again for `recordsFiltered`, before taking a page. Grouped or distinct queries are counted as result rows, not their underlying records.
3. It applies the requested order, offset and page size. `data` is only the returned page; `map()` runs afterwards. An offset past the last match gives `data: []` without changing the counts.

::: tip Base query checklist
Select only the fields you plan to expose, filter rows to the signed-in user before passing the builder, and avoid `limit()` and `offset()` on the base query. For large endpoints, validate `length` on the server and choose a stable default order.
:::

## Errors and validation

`Rmunate\EasyDatatable\Exceptions\DatatableException` signals invalid configuration or invalid DataTables input: for example, no processing mode, a missing `draw`, an order column not selected by the query, or a search callback returning something other than a builder. The package does not assign an HTTP status or convert the exception to JSON; Laravel's exception handling and your route validation control that.

See [troubleshooting](./troubleshooting.md) for what to check when a request fails.
