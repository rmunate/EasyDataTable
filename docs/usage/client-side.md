---
description: Load a SQLite catalog through Laravel Query Builder and map available stock before sending it to the browser.
---

# Client-side catalog <Badge type="tip" text="02 · Query Builder" />

Use client-side mode when the browser can hold every visible row. Start with the [prepared-data catalog](../other-features/data-preparated.md): keep its page route, Blade view and JSON route name, and replace only the row source.

## 1. Prepare the catalog

### Prepare local SQLite

Use an isolated **local** Laravel app and database for the demo. If you already have a working local SQLite connection, reuse it as long as it does not have a `products` table. Otherwise:

1. Confirm `php -m` lists `pdo_sqlite`. If it does not, enable the SQLite PDO extension in the CLI `php.ini` reported by `php --ini`.
2. Create an empty `database/database.sqlite` file **if it does not already exist**. In the app's `.env`, set `DB_CONNECTION=sqlite` and `DB_DATABASE` to the absolute path of that file. Do not point the demo at a shared or production database.
3. Run `php artisan config:clear` to load the updated `.env`.

### Import the products fixture

Save this file as `catalog-demo.sql` at your Laravel app root:

```sql [catalog-demo.sql]
CREATE TABLE products (
    id INTEGER PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    stock INTEGER NOT NULL,
    reserved INTEGER NOT NULL,
    active BOOLEAN NOT NULL
);

INSERT INTO products (id, name, stock, reserved, active) VALUES
    (1, 'Desk lamp', 12, 3, TRUE),
    (2, 'Monitor stand', 4, 1, TRUE),
    (3, 'USB-C adapter', 4, 4, TRUE),
    (4, 'Retired keyboard', 7, 0, FALSE);
```

Run `php artisan tinker` from the app root. Paste these lines into the Tinker prompt **once**, then exit the prompt with `exit`:

```php
\Illuminate\Support\Facades\DB::unprepared(file_get_contents(base_path('catalog-demo.sql')));
\Illuminate\Support\Facades\DB::table('products')->count();
```

The count should be **4**. If `tinker` is unavailable, install `laravel/tinker` in the demo app with `composer require --dev laravel/tinker` and retry. The fixture creates the example table, so importing it twice is not required.

## 2. Replace the JSON route

In `routes/web.php`, add the `DB` import and keep the other imports only once. Replace the existing `/examples/catalog/data` definition, leaving `Route::view('/examples/catalog', 'examples.catalog')` unchanged:

```php [routes/web.php]
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Rmunate\EasyDatatable\EasyDataTable;

Route::get('/examples/catalog/data', function () {
    $products = DB::table('products')
        ->where('active', true)
        ->select('name', 'stock', 'reserved')
        ->orderBy('name');

    return EasyDataTable::init()
        ->clientSide()
        ->query($products)
        ->map(fn ($row) => [
            'name' => $row->name,
            'stock' => max(0, $row->stock - $row->reserved),
        ])
        ->response();
})->name('examples.catalog.data');
```

Pass the **unexecuted** `Illuminate\Database\Query\Builder` to `query()`; EasyDataTable calls `get()` in client-side mode. Use `fromData()` if you already have the rows. `request()` and `search()` are server-side options and cannot be used here.

## 3. Verify the mapped rows

Open <http://127.0.0.1:8000/examples/catalog/data>. The endpoint returns `{"data":[...]}` with three products: **Desk lamp** (9), **Monitor stand** (3) and **USB-C adapter** (0). The inactive keyboard is not in the response, and `reserved` was used only to compute `stock`.

Refresh <http://127.0.0.1:8000/examples/catalog> and sort by **In stock**. With the view unchanged, the browser sorts those mapped values rather than the original `stock` database column.

::: tip Keep the access rules on the server
Apply visibility restrictions in the base query. A hidden column or a client-side filter does not prevent the browser from receiving data.
:::

## Next: move the work to SQL

If the full JSON payload slows down initial load or browser interaction, move to the [server-side catalog](./server-side.md). For a more involved client-side snapshot, run the [employee leave dashboard](./employee-leave.md).
