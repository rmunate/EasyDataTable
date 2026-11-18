# Client-side catalog <Badge type="tip" text="02 · Query Builder" />

Use client-side mode when the browser can hold every visible row. This example keeps the [first table's](../other-features/data-preparated) Blade view and route name, but switches the endpoint from a PHP array to a query.

## 1. Prepare the catalog

The example expects `products` with a name, stock, reserved stock and an active flag. Use your existing table or run this fixture in a **fresh local database**:

::: details Sample SQL
```sql
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
:::

## 2. Replace the data route

```php {6-9,12-17}
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

The view still expects `{"data":[...]}`. It shows **9** available desk lamps and hides the retired keyboard. The browser sorts the resulting `stock` numbers; it never receives `reserved` as a separate field.

::: tip Keep the access rules on the server
Apply visibility restrictions in the base query. A hidden column or a client-side filter does not prevent the browser from receiving data.
:::

Pass the query builder without calling `get()`. In this mode, `request()` and `search()` are not available. If you already have the rows, use `fromData()` instead.

## Next: move the work to SQL

If you would otherwise send thousands of rows per request, move to [server-side mode](./server-side). For a more involved client-side example, run the [employee leave snapshot](https://github.com/rmunate/EasyDataTable/tree/master/examples#employee-leave-dashboard).
