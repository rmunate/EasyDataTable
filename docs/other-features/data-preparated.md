# First table: prepared data <Badge type="tip" text="01 · Beginner" />

Start with three products and a single GET endpoint. `fromData()` accepts an array, a collection or another iterable of **rows** and selects client-side mode for you. No database or request parsing is needed.

## 1. Add the route

Copy this into `routes/web.php`:

<<< @/../examples/routes.php#catalog{4,7-11}

Go to `/examples/catalog/data` to check the response:

```json
{
  "data": [
    { "name": "Desk lamp", "stock": 12 },
    { "name": "Monitor stand", "stock": 4 },
    { "name": "USB-C adapter", "stock": 0 }
  ]
}
```

## 2. Render it in the browser

Place this view at `resources/views/examples/catalog.blade.php`, then open `/examples/catalog`. Search for `USB`, then sort by **In stock**. The browser does both without requesting another page.

::: details Complete Blade view (HTML, CDN assets and JavaScript)
<<< @/../examples/ClientSide/catalog.blade.php{html}
:::

::: tip Use a list, even for one row
Use `fromData([['name' => 'Desk lamp', 'stock' => 12]])`, not `fromData(['name' => 'Desk lamp', 'stock' => 12])`.
:::

## 3. Add a derived field

Use `map()` to normalize rows from a collection or combine values from multiple sources. The callback receives each row as an object, even if the input rows are arrays.

```php
use Rmunate\EasyDatatable\EasyDataTable;

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
```

The table still reads the same `name` and `stock` fields. Replace the array with a [query-backed catalog](../usage/client-side) when the source becomes a database.
