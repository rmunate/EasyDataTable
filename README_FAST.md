## Client Side
This type of table is used when we send all the data to the FrontEnd, and it is responsible for organizing, filtering, and generating any type of interactivity with the table. However, this type is not recommended for large volumes of data, as the client's experience may be affected while the library renders all the data, which could take a considerable amount of time.

### Route
```php
Route::get('/module/datatable', [ModuleController::class, 'dataTable']);
```

### Controller
```php
<?php
use Rmunate\EasyDatatable\EasyDataTable;

public function dataTable(Request $request)
{
    $query = DB::table('novedades')
            ->leftJoin('tipo_novedades', 'tipo_novedades.id', '=', 'novedades.tipo_novedad_id')
            ->leftJoin('empleados', 'empleados.id', '=', 'novedades.empleado_id')
            ->select(
                'empleados.cedula AS identification',
                'empleados.nombre AS employee',
                'tipo_novedades.nombre AS novelty_type',
                'novedades.descripcion AS description',
                'novedades.dias_calendario AS calendar_days',
                'novedades.dias_habiles AS business_days',
                'novedades.fecha_inicial AS initial_date',
                'novedades.fecha_final AS final_date',
            )
            ->where('empleados.empresa', $request->company); /* (Optional) */

    /* (Optional) */
    $permissionEdit = Auth::user()->can('novedades.editar');


    $datatable = new EasyDataTable();
    $datatable->clientSide();
    $datatable->query($query);
    
    /* (Optional) */
    $datatable->map(function($row) use ($permissionEdit){
        return [
            'identification' => $row->identification,
            'employee'       => strtolower($row->employee),
            'novelty_type'   => strtolower($row->novelty_type),
            'description'    => strtolower($row->description),
            'calendar_days'  => $row->calendar_days,
            'business_days'  => $row->business_days,
            'initial_date'   => date('d/m/Y', strtotime($row->initial_date)),
            'final_date'     => date('d/m/Y', strtotime($row->final_date)),
            "action" => [
                "editar" => $permissionEdit
            ]
        ];
    });
    return $datatable->response();
}
```

### JavaScript
```javascript
var dataTable = $('#datatable').DataTable({
    processing: true,
    responsive: true,
    pagingType: "full_numbers",
    ajax: {
        url: baseUrl + '/module/datatable',
        dataSrc: 'data'
    },
    columns: [
        { data: "identification" },
        { data: "employee" },
        { data: "novelty_type" },
        { data: "description" },
        { data: "calendar_days" },
        { data: "business_days" },
        { data: "initial_date" },
        { data: "final_date" },
        { data: "action",
            render: function (data, type, row, meta) {
                let btnEdit = '';
                if (data.editar) {
                    btnEdit = `<button class="btn btn-sm btn-info btn-edit" data-id="${row.identification}" data-employee="${row.employee}" title="Edit">
                                    <i class="fa flaticon-edit-1"></i>
                                </button>`;
                }
                return `<div class='btn-group'>${btnEdit}</div>`;
            },
            orderable: false
        }
    ],
    language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json"
    }
});
```

### HTML
```html
<script src="../jquery-3.6.0.min.js"></script>
<script src="../dataTables.min.js"></script>

<table id="datatable" class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Identification</th>
            <th>Employee</th>
            <th>Novelty Type</th>
            <th>Description</th>
            <th>Calendar Days</th>
            <th>Business Days</th>
            <th>Initial Date</th>
            <th>Final Date</th>
            <th>Action</th>
        </tr>
    </thead>
</table>
```

## Server Side
This type of table is used to handle high volumes of data. It only loads a limited amount of data from the backend on each interaction with the table. Commonly, it renders data in chunks, for example, up to a maximum of 100 records, depending on the table's pagination settings on the FrontEnd. In conclusion, this type of table is highly recommended if you want to maintain a fast and efficient interaction with the application.

### Route
```php
Route::get('/module/datatable', [ModuleController::class, 'dataTable']);
```

### Controller
```php
<?php
use Rmunate\EasyDatatable\EasyDataTable;

public function dataTable(Request $request)
{
    $query = DB::table('novedades')
            ->leftJoin('tipo_novedades', 'tipo_novedades.id', '=', 'novedades.tipo_novedad_id')
            ->leftJoin('empleados', 'empleados.id', '=', 'novedades.empleado_id')
            ->select(
                'empleados.cedula AS identification',
                'empleados.nombre AS employee',
                'tipo_novedades.nombre AS novelty_type',
                'novedades.descripcion AS description',
                'novedades.dias_calendario AS calendar_days',
                'novedades.dias_habiles AS business_days',
                'novedades.fecha_inicial AS initial_date',
                'novedades.fecha_final AS final_date',
            );

    /* (Optional) */
    $permissionEdit = Auth::user()->can('novedades.editar');

    $datatable = new EasyDataTable();
    $datatable->serverSide();
    $datatable->request($request);
    $datatable->query($query);

    /* Optional) */
    $datatable->map(function($row) use($editar){
        return [
            'identification' => $row->identification,
            'employee'       => strtolower($row->employee),
            'novelty_type'   => strtolower($row->novelty_type),
            'description'    => strtolower($row->description),
            'calendar_days'  => $row->calendar_days,
            'business_days'  => $row->business_days,
            'initial_date'   => date('d/m/Y', strtotime($row->initial_date)),
            'final_date'     => date('d/m/Y', strtotime($row->final_date)),
            "action" => [
                "editar" => $editar
            ]
        ];
    });

    /* Optional) */
    $datatable->search(function($query, $search){
        return $query->where(function($query) use ($search) {
                    $query->where('novedades.id', 'like', "%{$search}%")
                        ->orWhere('novedades.descripcion', 'like', "%{$search}%")
                        ->orWhere('tipo_novedades.nombre', 'like', "%{$search}%")
                        ->orWhere('empleados.nombre', 'like', "%{$search}%")
                        ->orWhere('empleados.cedula', 'like', "%{$search}%");
                });
    });
    return $datatable->response();

}
```

### JavaScript
```javascript
var dataTable = $('#datatable').DataTable({
    processing: true,
    responsive: true,
    serverSide: true,
    pagingType: "full_numbers",
    ajax: {
        url: baseUrl + "/module/datatable",
        dataType:"JSON",
        type:"GET"
    },
    columns: [
        { data: "identification" },
        { data: "employee" },
        { data: "novelty_type" },
        { data: "description" },
        { data: "calendar_days" },
        { data: "business_days" },
        { data: "initial_date" },
        { data: "final_date" },
        { data: "action",
            render: function (data, type, row, meta) {
                let btnEdit = '';
                if (data.editar) {
                    btnEdit = `<button class="btn btn-sm btn-info btn-edit" data-id="${row.identification}" data-employee="${row.employee}" title="Edit">
                                    <i class="fa flaticon-edit-1"></i>
                                </button>`;
                }
                return `<div class='btn-group'>${btnEdit}</div>`;
            },
            orderable: false
        }
    ],
    language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json"
    }
});
```

### HTML
```html
<script src="../jquery-3.6.0.min.js"></script>
<script src="../dataTables.min.js"></script>

<table id="datatable" class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Identification</th>
            <th>Employee</th>
            <th>Novelty Type</th>
            <th>Description</th>
            <th>Calendar Days</th>
            <th>Business Days</th>
            <th>Initial Date</th>
            <th>Final Date</th>
            <th>Action</th>
        </tr>
    </thead>
</table>
```