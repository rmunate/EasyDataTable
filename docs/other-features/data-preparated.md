# Prepared data

Use `fromData()` for rows you already have as an array, collection or other iterable. It selects client-side mode automatically. Pass a sequence of rows, not one associative row.

```php
use Rmunate\EasyDatatable\EasyDataTable;

return EasyDataTable::init()
    ->fromData([['name' => 'Ada'], ['name' => 'Lin']])
    ->map(fn ($row) => ['name' => strtoupper($row->name)])
    ->response();
// ['data' => [['name' => 'ADA'], ['name' => 'LIN']]]
```

With no `map()` callback, rows are returned unchanged in a zero-indexed list.
