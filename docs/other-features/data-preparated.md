---
description: Build a database-free Laravel table end to end, with copyable routes, a complete Blade view and expected JSON.
---

# First table: prepared data <Badge type="tip" text="01 · Beginner" />

Build a working Ajax catalog with three PHP rows and no database. You will create one route for the page and a second route for the JSON it loads. Start with a [running Laravel app and EasyDataTable installed](../getting-started/installation.md).

## 1. Add the routes

In your Laravel app, open `routes/web.php`. Keep the imports at the top, under the existing `<?php`, and don't repeat `use Illuminate\Support\Facades\Route;` if it is already there. Add both route definitions:

<<< @/../examples/routes.php#catalog{php}

`Route::view()` serves `examples.catalog`, which Laravel resolves to `resources/views/examples/catalog.blade.php`. The second route calls `fromData()` and returns a PHP array; Laravel serializes it as JSON. It needs no DataTables request parameters.

## 2. Verify the JSON endpoint

Open <http://127.0.0.1:8000/examples/catalog/data>. The response should be HTTP **200** with the following fields and values:

```json
{
  "data": [
    { "name": "Desk lamp", "stock": 12 },
    { "name": "Monitor stand", "stock": 4 },
    { "name": "USB-C adapter", "stock": 0 }
  ]
}
```

If you see HTML or a 404, run `php artisan route:list --path=examples/catalog` in the Laravel app and confirm both GET routes are listed.

## 3. Render the table in Blade

Create the `resources/views/examples` directory if necessary. Save this complete view as `resources/views/examples/catalog.blade.php`:

<<< @/../examples/ClientSide/catalog.blade.php{html}

- The `data-source` attribute resolves the URL from the named route `examples.catalog.data`; keep that name in sync with the route definition.
- `ajax` tells DataTables to read the top-level `data` list by default. Each `columns[].data` key must match a field in each JSON row: `name` and `stock`.
- jQuery loads before DataTables, and the initializer runs after the markup and scripts. `DataTable.render.text()` displays the product name as text.

Open <http://127.0.0.1:8000/examples/catalog>. You should see three products. Search for `USB` to see only **USB-C adapter**, clear the search, then click **In stock** to sort. In the browser's **Network** tab you should see the initial `/examples/catalog/data` request, but no new requests when you search or sort.

::: tip Use a list, even for one row
`fromData()` accepts an array, Laravel collection or other iterable and selects client-side mode. Pass `fromData([['name' => 'Desk lamp', 'stock' => 12]])` for a single row, or `fromData([])` for `{"data":[]}`.
:::

## 4. Optional: derive available stock

To subtract reserved stock, replace just the **JSON route** with this version. Keep the existing page route and imports:

```php
Route::get('/examples/catalog/data', function () {
    $rows = collect([
        ['name' => 'Desk lamp', 'stock' => 12, 'reserved' => 3],
        ['name' => 'USB-C adapter', 'stock' => 4, 'reserved' => 4],
    ]);

    return EasyDataTable::init()
        ->fromData($rows)
        ->map(fn ($row) => [
            'name' => $row->name,
            'stock' => max(0, $row->stock - $row->reserved),
        ])
        ->response();
})->name('examples.catalog.data');
```

The mapper receives each row as an object, even when the input rows are arrays. Refresh the table: it now shows two products, with **9** desk lamps and **0** adapters. The array returned by `map()` still has `name` and `stock`, so the view does not change and `reserved` does not appear in the JSON.

Next: [replace the array with a database query](../usage/client-side.md).
