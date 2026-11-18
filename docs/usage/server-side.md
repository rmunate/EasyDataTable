---
description: Wire DataTables server-side processing to Laravel and verify the exact search, sort and pagination response.
---

# Server-side catalog <Badge type="info" text="03 · Pagination" />

Let the database filter, sort and return one page at a time. Start with the [client-side catalog and its four fixture rows](./client-side.md). Keep the same page route, table markup and named Ajax route; change both the Laravel endpoint and the DataTables initializer.

## 1. Switch both ends to server-side mode

In `routes/web.php`, add the `Request` import (keep existing imports once) and **replace** the old JSON route. In `resources/views/examples/catalog.blade.php`, replace the JavaScript inside the final inline `<script>` tag; keep the two CDN script tags and table markup.

::: code-group

```php [routes/web.php]
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Rmunate\EasyDatatable\EasyDataTable;

Route::get('/examples/catalog/data', function (Request $request) {
    $request->validate(['length' => ['required', 'integer', 'between:1,100']]);

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
    lengthMenu: [10, 25, 50, 100],
    ajax: {
        url: catalog.dataset.source,
        headers: { Accept: 'application/json' }
    },
    columns: [
        { data: 'name', render: DataTable.render.text() },
        { data: 'stock', searchable: false }
    ]
});
```

:::

The query passes the original `stock` field, so **In stock** now means physical stock rather than available stock after reservations. This keeps sorting in SQL. The `search()` callback searches only the `name` column and returns the builder to EasyDataTable. Laravel validates the page size independently of the frontend menu, and the `Accept` header gives the browser JSON if Laravel validation fails.

## 2. Inspect a draw in the browser {#inspect-a-draw}

Open <http://127.0.0.1:8000/examples/catalog>. In Developer Tools, open **Network**, then search for `lamp`. Select the new `/examples/catalog/data` request and look at the query string and response. Search and sort now cause new requests.

To reproduce a draw without the UI, paste this URL into your browser. The URL encodes the brackets in keys such as `search[value]`:

```text
http://127.0.0.1:8000/examples/catalog/data?draw=2&start=0&length=10&search%5Bvalue%5D=lamp&columns%5B0%5D%5Bdata%5D=name&order%5B0%5D%5Bcolumn%5D=0&order%5B0%5D%5Bdir%5D=asc
```

The fixture returns:

```json
{
  "draw": 2,
  "recordsTotal": 3,
  "recordsFiltered": 1,
  "data": [{ "name": "Desk lamp", "stock": 12 }]
}
```

| Field | What it means |
| --- | --- |
| `draw` | The request's integer counter. It lets DataTables ignore responses that arrive out of order. |
| `recordsTotal` | Rows in the base query, including authorization and any fixed filters. |
| `recordsFiltered` | Rows after the global search, before pagination. |
| `data` | Rows on the requested page, optionally transformed by `map()`. |

Opening `/examples/catalog/data` **without** paging parameters is no longer a valid request. Use the table or send `draw`, `start` and `length` yourself.

## 3. Customize search and order safely

- **Select public fields explicitly.** For `select('products.name as product')`, use `{ data: 'product' }` in JavaScript. Give joined fields distinct aliases. Do not expose private fields in the projection just to hide them in the browser.
- **Keep display-only fields out of SQL ordering.** A field added by `map()` does not exist in the SQL query. Set `orderable: false` and exclude it from the initial `order`, or project a sortable field from a subquery.
- **Expect one requested sort column.** Use `orderMulti: false`. If you want the builder's `orderBy()` to apply on the first load, set `order: []` in the initializer. A requested order replaces the query's existing order.
- **Choose server search fields in PHP.** The default global search uses SQL `LIKE` across resolved fields; a JavaScript `searchable: false` does not restrict that server search. Use `search()` for a smaller set, grouping any `orWhere()` conditions under a nested `where()` to preserve scope.
- **Leave pagination to EasyDataTable.** Do not add paging to the base query. Although the package accepts `length: -1` for all remaining rows, this route rejects it and caps the page size. Keep this cap when adapting the endpoint.

See the [request and response reference](../reference/api.md#server-side-request) for all supported fields. For joins, external filters and permission-gated actions, [build the employee leave dashboard](./employee-leave.md).
