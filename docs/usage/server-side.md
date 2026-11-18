# Server-side tables

Use server-side mode when you want the database to filter, sort and return one page at a time. Pass a Laravel **query builder**, not the result of `get()`. Apply authorization scopes before passing the query, and leave pagination to EasyDataTable.

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Rmunate\EasyDatatable\EasyDataTable;

Route::get('/module/datatable', function (Request $request) {
    return EasyDataTable::init()
        ->serverSide()
        ->request($request)
        ->query(DB::table('products')->select('id', 'products.name AS product'))
        ->response();
});
```

```js
$('#datatable').DataTable({
    serverSide: true,
    ajax: '/module/datatable',
    columns: [{ data: 'id' }, { data: 'product', render: $.fn.dataTable.render.text() }]
});
```

The response contains the integer `draw`, `recordsTotal` (before search), `recordsFiltered` (after search, before pagination), and a `data` array. The package works on a copy of the builder.

- DataTables supplies `draw`, `start` and `length`. `length: -1` requests all remaining rows; avoid offering that option for large datasets.
- Global search uses `search.value` (or a plain `search` string) and SQL `LIKE` on selected fields. Qualify joined columns and give duplicate names distinct aliases. An implicit `*` is supported for a single unaliased table; prefer an explicit list of public fields.
- The first `order` instruction controls sorting. `columns[i].data` must match a selected column or alias. Without a requested order the builder's order is preserved; set a stable default order when paging. Mark mapped-only or raw-expression columns `orderable: false`.
- For custom filtering, return the builder from `search()`. Group `orWhere` conditions so they do not bypass the base query's restrictions. Use `map()` to format rows **after** pagination.

```php
$table->search(fn ($query, $term) => $query->where('products.name', 'like', "%{$term}%"));
```

See the [complete controller and HTML example](https://github.com/rmunate/EasyDataTable/tree/master/examples/ServerSide). Both example pages use the same `examples/table.js`.
