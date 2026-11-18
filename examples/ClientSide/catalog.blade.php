<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catalog | EasyDataTable</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css">
</head>
<body>
    <h1>Sample catalog</h1>
    <table id="catalog" data-source="{{ route('examples.catalog.data') }}">
        <thead>
            <tr><th>Product</th><th>In stock</th></tr>
        </thead>
        <tbody></tbody>
    </table>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script>
        const catalog = document.querySelector('#catalog');
        new DataTable(catalog, {
            ajax: catalog.dataset.source,
            columns: [
                { data: 'name', render: DataTable.render.text() },
                { data: 'stock' }
            ]
        });
    </script>
</body>
</html>
