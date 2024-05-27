---
title: Customize JavaScript Alias
editLink: true
outline: deep
---

# Client-Side Configuration

The main feature of a ClientSide DataTable is that all data and data manipulation logic are handled on the client side, i.e., in the user's web browser, rather than making requests to the web server for each interaction. This means that all the data needed to populate the table is initially loaded in the user's browser, and operations such as search, filtering, sorting, and pagination are performed without the need to communicate with the server.

If you want to download the example, you can find it [here](https://github.com/rmunate/EasyDataTable/tree/master/examples/ClientSide).

Let's see how to set up the backend for a ClientSide table. Remember that in these cases, all the information must be delivered to the Frontend, which is not recommended for very large data sizes.

## Route
Define a GET route without sending arguments via the URL; this will be done implicitly by the JavaScript DataTable library.

```php
Route::get('/module/datatable', [NameController::class, 'dataTable']);
```

## Controller

Now that we have the route, let's proceed to create the method in the corresponding controller. This method will always handle Query Builder. For now, it's not possible to use Eloquent because we need to know the names of the columns we want to render in the Frontend, and Query Builder offers more convenient ways to standardize this.

```php
use Rmunate\EasyDatatable\EasyDataTable;

class NameController extends Controller
{
    /**
     * Define a method to handle the GET request receiving a Request type variable.
     */
    public function dataTable(Request $request)
    {
        /**
         * Create a query with Query Builder; you can apply all the conditions you require, just DO NOT apply the final get() method.
         */
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
                'novedades.fecha_final AS final_date'
            );

        /**
         * (Optional) Sometimes, to determine if a user has permissions for some action in the table rows, you need to make queries like these.
         */
        $permissionEdit = Auth::user()->can('novedades.editar');

        /**
         * Using the library is as simple as using the following code.
         */
        $datatable = new EasyDataTable();
        $datatable->clientSide(); /* Mandatory / Required */
        $datatable->query($query);
        $datatable->map(function ($row) use ($permissionEdit) {
            /**
             * (Optional) If you need to customize how the information is displayed in the table, the map() method will be very helpful.
             * Additionally, if you need to send additional data or perform validations, you can apply the logic here.
             * It's important that the alias of each value in the query is the same value used in the array, as shown below.
             */
            return [
                'identification' => $row->identification,
                'employee'       => strtolower($row->employee),
                'novelty_type'   => strtolower($row->novelty_type),
                'description'    => strtolower($row->description),
                'calendar_days'  => $row->calendar_days,
                'business_days'  => $row->business_days,
                'initial_date'   => date('d/m/Y', strtotime($row->initial_date)),
                'final_date'     => date('d/m/Y', strtotime($row->final_date)),
                'action'         => [
                    'editar' => $permissionEdit,
                ],
            ];
        });

        /**
         * This method returns the structure built as required by the DataTable library on the Frontend.
         */
        return $datatable->response();
    }
}
```

## JavaScript

Below is a basic example of DataTable configuration. Here, you should use whatever you need from what DataTable offers as a JS library with JQuery. In this example, you will find what we consider necessary for you to create a table that covers the standard needs of a web software.

```javascript
/* Create a global variable in the JS file to store the value of the data table to be built. This will be very useful when you need to update the information of the table without having to reload the page. */
var dataTable;

/* Define this variable that will contain the base of the server where the requests will be sent */
var baseUrl = window.location.protocol + "//" + window.location.host;

/* When the file is loaded */
$(document).ready(function () {
  /* Here we will assign to the variable an instance of dataTable; make sure it's the same ID as the table tag */
  dataTable = $("#datatable").DataTable({
    processing: true,
    responsive: true,
    pagingType: "full_numbers",
    ajax: {
      /* Make sure the route (web.php) is of type GET */
      url: baseUrl + "/modulo/datatable",
      dataSrc: "data",
    },
    columns: [
      /* The name must be the same as that of the associative array returned from the BackEnd */
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
          let btnEdit = "";
          if (data.editar) {
            btnEdit = `<button class="btn btn-sm btn-info btn-edit" data-id="${row.identification}" data-employee="${row.employee}" title="Edit">
                            <i class="fa flaticon-edit-1"></i>
                       </button>`;
          }
          return `<div class='btn-group'>${btnEdit}</div>`;
        },
        orderable: false,
      },
    ],
    /* Remember that you can apply language settings for your region. */
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json",
    },
  });
});
```

## HTML

In the HTML, you should have a structure similar to the following. Make sure that the number of columns defined in the JS matches those defined in the HTML:

```html
<!-- Make sure you have imported jQuery and the DataTable library -->

<!-- You should create the structure of the table and assign it an ID that will be used as a selector to turn it into a "DataTable". Also, make sure that the headers match the number of columns configured in JavaScript. -->
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