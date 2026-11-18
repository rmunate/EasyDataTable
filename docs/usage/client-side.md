# Client-side tables

Use client-side mode when the browser can hold the entire result set. The endpoint returns `{"data":[...]}`; DataTables handles paging, searching and ordering without another request.

```php
use Illuminate\Support\Facades\DB;
use Rmunate\EasyDatatable\EasyDataTable;

return EasyDataTable::init()
    ->clientSide()
    ->query(DB::table('products')->select('id', 'name'))
    ->map(fn ($row) => ['id' => $row->id, 'name' => $row->name])
    ->response();
```

`map()` is optional. For an existing array or collection, use [prepared data](../other-features/data-preparated) instead. Do not pass `request()` or `search()` in client-side mode.

The [complete example](https://github.com/rmunate/EasyDataTable/tree/master/examples/ClientSide) uses the shared `examples/table.js` and an authenticated GET route at `/module/datatable`. Adapt the controller name, tables, company scope and permissions to your application.
