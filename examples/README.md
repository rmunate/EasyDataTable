# EasyDataTable examples

These are copy-in examples for a Laravel application. They are not a separate Laravel project. Run Laravel locally with `php artisan serve` after installing the package (`composer require "rmunate/easy-datatable:^3.0"`).

## Catalog without a database

1. Copy [`ClientSide/catalog.blade.php`](ClientSide/catalog.blade.php) to `resources/views/examples/catalog.blade.php`.
2. Copy the `catalog` region from [`routes.php`](routes.php) into `routes/web.php` (keep the imports only once).
3. Open `http://127.0.0.1:8000/examples/catalog`. Search for `USB` or sort by stock. `/examples/catalog/data` returns all three rows.

The [client-side guide](../docs/usage/client-side.md) shows how to replace the array with a query; the [server-side guide](../docs/usage/server-side.md) uses the same catalog and moves pagination to SQL.

## Employee leave dashboard

1. Copy [`ServerSide/LeaveTableController.php`](ServerSide/LeaveTableController.php) to `app/Http/Controllers/LeaveTableController.php`, [`ServerSide/leave.blade.php`](ServerSide/leave.blade.php) to `resources/views/examples/leave.blade.php`, and [`table.js`](table.js) to `public/examples/leave/table.js`.
2. Add the `leave-routes` region of [`routes.php`](routes.php) to `routes/web.php`, with the `Route` and `LeaveTableController` imports. Keep the `auth` middleware.
3. Create the tables in a **fresh local SQLite database** using [`ServerSide/demo.sql`](ServerSide/demo.sql). If Artisan Tinker is installed, copy the file to the Laravel project root as `leave-demo.sql`, run `php artisan tinker`, and execute `DB::unprepared(file_get_contents(base_path('leave-demo.sql')));` once. Never load these sample records into a production database.
4. Sign in with an application user whose `company_id` is `10`. If the app does not have authentication yet, set it up before using these routes. Use your app's company membership and define the `review-leave-requests` Gate for users allowed to review leave. For example, in `AppServiceProvider::boot()`, `Gate::define('review-leave-requests', fn (User $user) => $user->role === 'hr');` (with `Illuminate\Support\Facades\Gate`, `App\Models\User`, and your own trusted role field). Neither `company_id` nor `role` is a default Laravel user field; adapt the example to your application.

| Open in the Laravel app | Try this |
| --- | --- |
| `/examples/leave/client` | Search for `Vacation`. The browser searches the small dataset it already fetched. |
| `/examples/leave/server` | Select **Pending**, search for `Vacation`, and set the date to `2026-10-10`. Only request `104` is returned. |
| `/examples/leave/review/104` | A reviewer in company `10` can open details. A non-reviewer gets `403`; a reviewer cannot open the other company's request `201`. |

Both views use DataTables 2; the leave view also loads the Responsive extension. To reuse the same table with a Metronic bundle, see the [Metronic integration guide](../docs/usage/metronic.md).
