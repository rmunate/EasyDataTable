---
description: Diagnose EasyDataTable installation, empty tables, incorrect counts, SQL errors and rejected DataTables requests.
---

# Troubleshooting

Start with the exact request that failed. The table may look empty even when the real cause is an HTTP redirect, a JavaScript error or a field name mismatch.

## Installation and startup

| Symptom | Check and fix |
| --- | --- |
| Composer cannot resolve `^3.0`. | Run `php -v` and `php artisan --version`. Compare them with the [version matrix](../getting-started/versions.md). From the app root, `composer why-not rmunate/easy-datatable 3.0.0` can identify a conflicting package or PHP constraint. |
| `Class ... EasyDataTable not found`. | Run `composer show rmunate/easy-datatable` in the Laravel app, then check the exact import: `use Rmunate\EasyDatatable\EasyDataTable;`. Installing in a clone of this repository does not install it in your app. |
| `could not find driver`. | Run `php -m` in the same terminal that starts Artisan. Enable `pdo_sqlite` for the demo SQLite database, using `php --ini` to locate the configuration to edit. Then restart `php artisan serve`. |
| The database-free example fails on a `sessions` query. | For a fresh local demo with no database, set `SESSION_DRIVER=file` in `.env`, then run `php artisan config:clear`. Do not change session drivers in an existing production application to fix a demo. |

## The table is blank or reports an Ajax error

1. **Open the browser console.** If `DataTable` is undefined, confirm the frontend assets finished loading and that jQuery loads before DataTables in the standalone recipes. Check failed CDN requests and your Content Security Policy. If the console says the table cannot be reinitialized, remove the duplicate initializer.
2. **Open Network and select the Ajax request.** For the catalog, look for `/examples/catalog/data`; for the dashboard, look for `/examples/leave/server-data` or `/examples/leave/client-data`. If no request was sent, confirm the initializer runs after the markup and the table's `data-source` matches the named Laravel route.
3. **Check the response and status.** HTTP **200** should contain the [JSON contract](./api.md), not an HTML page. `302`/`401` usually means authentication is needed; sign in using the same app URL and browser session. `403` means access is denied. For `404`, compare `data-source` with `php artisan route:list --path=examples`. For `422`, read the Laravel validation errors. For `500`, check the configured Laravel log (usually `storage/logs/laravel.log`).
4. **Compare fields.** DataTables' “Requested unknown parameter” warning means a JSON field is missing or the row format is different from what `columns` expects. Inspect the first row in `data` and compare every `columns[].data` key. Include keys with `null` rather than omitting them, and give nullable cells a renderer or `defaultContent` if needed.

::: tip Make validation errors readable
Use `Accept: application/json` in the Ajax request configuration, as in the [server-side catalog](../usage/server-side.md). It helps Laravel return a JSON validation error instead of redirecting to a page.
:::

## Search, sorting and pagination

| Symptom | Check and fix |
| --- | --- |
| `Invalid or missing integer 'draw'`, `'start'` or `'length'`. | Turn on `serverSide: true` in DataTables when the PHP endpoint uses `serverSide()`. A manual request must send the [required draw inputs](./api.md#server-side-request). |
| `Server-side mode requires query() and request()`. | Pass the unexecuted builder and the incoming Laravel request before `response()`. Use `fromData()` without request options if your rows are already prepared. |
| `The order column must be a selected column or alias`. | Compare the requested `order[0][column]` with that index in `columns`. Use an explicit SQL projection with matching names. For a `map()`-only field, set `orderable: false` and remove it from any initial `order`. |
| The query cannot find a column or it is ambiguous. | Qualify joined columns in Laravel (`employees.name`) and give them unique aliases (`employee`). Use the alias in JavaScript. For an aggregate, project the alias in an outer query. |
| Counts differ from the page length. | That is expected: the base scope and external filters determine `recordsTotal`; global search determines `recordsFiltered`; `data.length` counts only the current page. Remove any paging from the base builder. |
| A server-side search returns too many rows or searches a hidden field. | Limit the query to permitted rows and public columns. Implement `search()` for the allowed fields, and wrap `OR` conditions in a nested `where()` so base restrictions still apply. |

## Before shipping a table

- Test the Ajax endpoint as a permitted user and as an account that should not see the same rows. Check both JSON and action routes.
- Keep permission checks and validation for status, dates and page size in Laravel, not just in JavaScript.
- Display user-provided text with an escaped renderer. Check timestamps, nullable columns, no results and a partial last page.
- Measure payload size and response time with representative data. In server-side mode, inspect the queries for the base count, filtered count and page; add appropriate indexes for the filters and joins.

If you can reproduce a failure with a small dataset, [report it with the request, response and versions](../contribute/report-bugs.md).
