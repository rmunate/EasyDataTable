---
description: Run a company-scoped Laravel dashboard with joins, status and date filters, and permission-gated review links.
---

# Employee leave dashboard <Badge type="warning" text="04 · Advanced" />

Build a company-scoped dashboard from three related tables: `leave_requests`, `employees` and `leave_types`. The example has a client-side snapshot and a server-side dashboard. They share the query, row mapper, Blade view and JavaScript initializer.

[Try the interactive preview first](../examples/#try-the-dashboard). It runs in the documentation site without Laravel. Follow the steps below to run the real queries and check permissions.

## 1. Set up a local demo

Use a local Laravel app with [EasyDataTable installed](../getting-started/installation.md), [SQLite configured](./client-side.md#prepare-local-sqlite), and a working login. A plain Laravel skeleton does not include a login screen; start from an authenticated app or install a [Laravel starter kit](https://laravel.com/docs/starter-kits) for your version before continuing. Keep the `auth` middleware.

### Import the leave fixture

Save [`demo.sql`](https://github.com/rmunate/EasyDataTable/blob/master/examples/ServerSide/demo.sql) as `leave-demo.sql` at the Laravel app root. Run `php artisan tinker` and enter these lines once:

```php
\Illuminate\Support\Facades\DB::unprepared(file_get_contents(base_path('leave-demo.sql')));
\Illuminate\Support\Facades\DB::table('leave_requests')->count();
```

Expect **8** requests across two companies. Exit the Tinker prompt with `exit`. Import into an isolated demo database, not into a database with existing leave tables or real employee data.

### Configure demo accounts and review permission

The controller expects a trusted `company_id` on the signed-in user and checks the `review-leave-requests` Gate. If your application already has company membership and a permissions policy, use those. For an isolated Laravel 13 demo using the default `App\Models\User`, use this setup:

::: details Add demo user fields and accounts
1. If your `users` table has neither field, run `php artisan make:migration add_leave_demo_fields_to_users_table --table=users`. Replace the generated `up()` and `down()` methods with these. The generated file already imports `Schema` and `Blueprint`. Skip this migration if both fields exist; if just one is missing, add and drop only that field.

   ```php
   public function up(): void
   {
       Schema::table('users', function (Blueprint $table) {
           $table->unsignedBigInteger('company_id')->nullable();
           $table->string('role')->default('employee');
       });
   }

   public function down(): void
   {
       Schema::table('users', function (Blueprint $table) {
           $table->dropColumn(['company_id', 'role']);
       });
   }
   ```

2. Run `php artisan migrate` on the demo database. Open `php artisan tinker`, paste the following and note the generated local password for the three demo accounts. Enter `exit` when done.

   ```php
   $password = bin2hex(random_bytes(12));

   foreach ([
       'reviewer@example.test' => [10, 'hr'],
       'employee@example.test' => [10, 'employee'],
       'other-reviewer@example.test' => [20, 'hr'],
   ] as $email => [$companyId, $role]) {
       $user = \App\Models\User::firstOrNew(['email' => $email]);
       $user->forceFill([
           'name' => $email,
           'company_id' => $companyId,
           'role' => $role,
           'password' => \Illuminate\Support\Facades\Hash::make($password),
           'email_verified_at' => now(),
       ])->save();
   }

   $password;
   ```
:::

For those demo accounts, add `use App\Models\User;` and `use Illuminate\Support\Facades\Gate;` to `app/Providers/AppServiceProvider.php` if they are not present. Inside the existing `boot()` method, add:

```php
Gate::define('review-leave-requests', static fn (User $user): bool => $user->role === 'hr');
```

## 2. Copy the dashboard files

Copy all three files from the repository into the Laravel app, creating the destination directories if needed:

| Source in EasyDataTable | Destination in your Laravel app |
| --- | --- |
| [`LeaveTableController.php`](https://github.com/rmunate/EasyDataTable/blob/master/examples/ServerSide/LeaveTableController.php) | `app/Http/Controllers/LeaveTableController.php` |
| [`leave.blade.php`](https://github.com/rmunate/EasyDataTable/blob/master/examples/ServerSide/leave.blade.php) | `resources/views/examples/leave.blade.php` |
| [`table.js`](https://github.com/rmunate/EasyDataTable/blob/master/examples/table.js) | `public/examples/leave/table.js` |

Add `use App\Http\Controllers\LeaveTableController;` and `use Illuminate\Support\Facades\Route;` to the top of `routes/web.php` (keep each import once), then add the route group:

<<< @/../examples/routes.php#leave-routes{php}

Run `php artisan route:list --path=examples/leave`. You should see **five** GET routes: two pages, two JSON endpoints and the review endpoint. Both pages use the same Blade file. The `serverSide` value passed by the route determines which named endpoint and which browser controls the view uses.

## 3. Understand scope, search and actions

### Join only rows the current user may see

<<< @/../examples/ServerSide/LeaveTableController.php#scoped-query{php}

The company comes from the signed-in user, not from a browser parameter. The explicit projection defines the public field names: JavaScript sorts `employee`, but the query searches `employees.name`. The left join keeps requests with no leave type.

### Apply external filters before global search

The browser sends `status` and `started_since` on each server-side draw. The controller validates both filters before using them. It also caps each request at 100 rows; the browser's page-size menu offers 5, 10 and 25.

<<< @/../examples/ServerSide/LeaveTableController.php#server-action{php}

Status and start date restrict the **base query** and therefore `recordsTotal`. Global search applies on top and determines `recordsFiltered`.

::: warning Keep OR conditions inside one group
The intended condition is `company AND filters AND (employee OR code OR type OR reason)`. An `orWhere()` added directly to the base builder can bypass the company restriction.
:::

### Format rows and authorize actions

<<< @/../examples/ServerSide/LeaveTableController.php#row-map{php}

Keep dates as ISO strings for sorting. The frontend displays them in the user's locale, escapes names and reasons as text, and creates a review link only when `review_url` is non-null. `review_url` is a mapped field, so its column is not orderable.

::: warning A hidden action is not authorization
The review route checks the Gate **and** loads the pending record within the user's company. The sample GET route returns details without updating them. A real write action needs its own authorization and CSRF protection.
:::

## 4. Run and verify both modes

From the app root, start `php artisan serve --host=127.0.0.1 --port=8000`. Open the app in your browser and sign in with `reviewer@example.test` (company `10`). Append these paths to `http://127.0.0.1:8000`:

| What to open or do | What you should see |
| --- | --- |
| `/examples/leave/client` | Seven visible requests total. Searching for `Vacation` leaves three matches without a new Ajax request. |
| `/examples/leave/server` | Seven visible requests total, five on the first page. Searching, sorting and paging send new Ajax requests. |
| Select **Pending** on the server page, leaving the other filters empty. | `recordsTotal: 3`, `recordsFiltered: 3`. |
| Search for `Vacation`, then set the date to `2026-10-10`. | The date and status leave two rows before search. The Ajax response has `recordsTotal: 2`, `recordsFiltered: 1` and only request `104` in `data`. |
| `/examples/leave/review/104` as the reviewer, then as `employee@example.test`. | The reviewer receives JSON details. The employee receives **403** and does not see review links on the dashboard. |
| `/examples/leave/review/201` as the company `10` reviewer. | **404**. Signing in as `other-reviewer@example.test` in company `20` instead shows only request `201` in the dashboard and allows reviewing it. |

Read `recordsTotal` and `recordsFiltered` in the `/examples/leave/server-data` response in the browser's **Network** tab. Clear filters before switching accounts.

### Read the Blade and JavaScript together

The initializer waits 300 milliseconds after typing before searching. Changing status or date reloads the Ajax source and resets pagination. Responsive prioritizes the employee and status columns on small screens; the error region surfaces invalid filters or a failed request.

::: code-group
<<< @/../examples/ServerSide/leave.blade.php{html} [Blade]
<<< @/../examples/table.js [JavaScript]
:::

Use the same initializer with [Metronic and an external toolbar](./metronic.md).

## Bonus: report by leave type

To sort aggregate counts, give them a name in an outer query. Add this method to the same `LeaveTableController` class if you want a separate report endpoint:

```php
public function report(Request $request): array
{
    $request->validate(['length' => ['required', 'integer', 'between:1,100']]);

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
}
```

Inside the existing authenticated `examples/leave` route group, add:

```php
Route::get('/report-data', [LeaveTableController::class, 'report'])->name('report-data');
```

Open `/examples/leave/report-data?draw=1&start=0&length=10` while signed in to company `10`. The totals are both **4**, and `request_count` values are `3`, `2`, `1`, and `1`. One row now represents a leave type, including a `null` type for uncategorized requests. A report table would use `leave_type` and `request_count` as its `columns[].data` keys, with `defaultContent: 'Uncategorized'` for the nullable type.
