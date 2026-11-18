# Server-side catalog <Badge type="info" text="03 · Pagination" />

When a table grows, let the database filter, sort and return one page at a time. Reuse the [catalog fixture and Blade view](./client-side) and keep the route name the same. In this example, **In stock** means physical stock, so it can be ordered by a projected database field instead of a mapped value.

## 1. Give the query and request to EasyDataTable

::: code-group

```php [routes/web.php]
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Rmunate\EasyDatatable\EasyDataTable;

Route::get('/examples/catalog/data', function (Request $request) {
    $products = DB::table('products')
        ->where('active', true)
        ->select('name', 'stock')
        ->orderBy('id');

    return EasyDataTable::init()
        ->serverSide()
        ->request($request)
        ->query($products)
        ->search(fn ($query, $term) => $query->where('name', 'like', "%{$term}%"))
        ->response();
})->name('examples.catalog.data');
```

```js [Replace the Blade initializer]
const catalog = document.querySelector('#catalog');
new DataTable(catalog, {
    serverSide: true,
    processing: true,
    orderMulti: false,
    ajax: catalog.dataset.source,
    columns: [
        { data: 'name', render: DataTable.render.text() },
        { data: 'stock', searchable: false }
    ]
});
```

:::

The callback searches only the text column. It returns the supplied builder so that the package can count the filtered rows and then fetch the requested page.

## 2. Inspect a draw

Open the browser's Network tab and search for `lamp`. The request contains `draw`, `start`, `length`, `columns`, `order`, and `search[value]`. With the catalog fixture, a request for the first page with `draw=2` returns:

```json
{
  "draw": 2,
  "recordsTotal": 3,
  "recordsFiltered": 1,
  "data": [{ "name": "Desk lamp", "stock": 12 }]
}
```

| Field | What it counts |
| --- | --- |
| `recordsTotal` | Rows in the base query, including authorization and any fixed filters. |
| `recordsFiltered` | Rows after the global search, before pagination. |
| `data` | Rows on the requested page, optionally transformed by `map()`. |

## 3. Customize search and order safely

- **Use selected fields or aliases.** For `select('products.name as product')`, use `{ data: 'product' }` in JavaScript. Give joined fields distinct aliases.
- **Sort computed display values deliberately.** A field added by `map()` does not exist in the SQL query. Set `orderable: false`, or project a sortable field from a subquery.
- **Only the first `order` instruction is applied.** Use `orderMulti: false` to match that behavior. If no sort arrives, the builder's default order is preserved.
- **Default search uses SQL `LIKE`.** Use `search()` to choose which fields to search; group multiple `orWhere()` conditions so they do not bypass the base query. Prefer a list of public fields to `*`.
- **Leave pagination to EasyDataTable.** DataTables sends `length: -1` for all remaining rows; remove that option and validate an upper limit for large or public endpoints.

Ready for joins, external filters and permission-gated actions? Build the [employee leave dashboard](./employee-leave).
