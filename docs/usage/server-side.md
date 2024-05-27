---
title: Share Specific Values with JavaScript
editLink: true
outline: deep
---

# Server-Side Configuration

The primary feature of a ServerSide DataTable is that most data-related operations are performed on the server-side rather than the client-side. Here are some key aspects of a ServerSide DataTable:

**Efficient Data Loading:** In a ServerSide DataTable, data is loaded from the server as the user navigates through the table or performs actions such as searching, filtering, sorting, and pagination. This allows for managing large data sets without overburdening the user's browser.

**Server Requests:** When an action requiring data manipulation, such as sorting the table, is performed, the DataTable sends a request to the web server. The server processes the request and returns the corresponding data.

**Efficient Filtering and Searching:** Filtering and searching are performed on the server-side, meaning only relevant data is sent to the client. This is especially useful when working with large data sets.

**Security:** By performing most operations on the server, greater security and authorization control can be implemented to ensure that users only access data they are authorized to view.

If you want to download the example, you can find it [here](https://github.com/rmunate/EasyDataTable/tree/main/examples/ServerSide).

Here's how to set up the backend for a ServerSide table:

## Route

You need to define a GET route without sending any arguments via the URL; this will be implicitly handled by the JS DataTable library.

```php
Route::get('/module/datatable', [NameController::class, 'dataTable']);
```

## Controller

Now that we have the route, let's proceed to create the method in the corresponding controller. In this case, the method will handle Query Builder. It's not possible to use Eloquent because we need to know the names of the columns that we want to render in the Front, and Query Builder offers more convenient ways to standardize this.

```php
use Rmunate\EasyDatatable\EasyDataTable;

class NameController extends Controller
{
    /**
     * Defines a method that handles the GET route receiving a Request variable.
     */
    public function dataTable(Request $request)
    {
        /**
         * Creates a query with Query Builder, you can apply all the conditions you need, just DO NOT apply the final get() method.
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
                'novedades.fecha_final AS final_date',
            );

        /**
         * (Optional) Sometimes, to determine if a user has permissions on some action in the table rows, you must perform queries like these.
         */
        $permissionEdit = Auth::user()->can('novedades.editar'); /* (Optional) */

        /**
         * Using the library is as simple as using the following code.
         */
        $datatable = new EasyDataTable();
        $datatable->serverSide(); /* Mandatory / Required */
        $datatable->request($request);
        $datatable->query($query);
        $datatable->map(function ($row) use ($editar) {

            /**
             * (Optional) If you need to customize how the information is displayed in the table, the map() method will be very helpful.
             * Also, if you need to send additional data or perform validations, you can apply the logic here.
             * It's important that the alias of each value in the query matches the value used in the array, as shown below.
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
                    'editar' => $editar,
                ],
            ];
        });
        $datatable->search(function ($query, $search) {
            /* This method will be very useful to define which filters the backend should execute when values are entered in the search field. The variable $search will contain this value. Remember to use the table.field structure in the conditions and not the aliases. */
            return $query->where(function ($query) use ($search) {
                $query->where('novedades.id', 'like', "%{$search}%")
                    ->orWhere('novedades.descripcion', 'like', "%{$search}%")
                    ->orWhere('tipo_novedades.nombre', 'like', "%{$search}%")
                    ->orWhere('empleados.nombre', 'like', "%{$search}%")
                    ->orWhere('empleados.cedula', 'like', "%{$search}%");
            });
        });

        /**
         * This method returns the built structure as required by the DataTable library on the Front.
         */
        return $datatable->response();
    }
}
```

## JavaScript

Below is a basic example of DataTable configuration. Here, it's a matter of using what you need from what DataTable offers as a JS library with JQuery. In this example, you'll find what we consider necessary for creating a table that covers the standard needs of a web software.

```javascript
/* Create a global variable in the JS file to store the value of the data table to be built. This will be very useful when you need to update the table information without having to reload the page. */
var dataTable;

/* Define this variable that will contain the base URL of the server where the requests will be sent */
var baseUrl = window.location.protocol + "//" + window.location.host;

/* When the file is loaded */
$(document).ready(function () {
  /* Here we will assign to the variable an instance of dataTable; make sure it's the same ID as the table tag */
  dataTable = $("#datatable").DataTable({
    processing: true,
    serverSide: true, /* Note, this value must be true to enable communication with the server */
    responsive: true,
    pagingType: "full_numbers",
    ajax: {
      /* Make sure the route (web.php) is of type GET */
      url: baseUrl + "/modulo/datatable",
      dataSrc: "data",
    },
    columns: [
      /* The name must be the same as the one from the associative array returned from the BackEnd */
      { data: "identification" },
      { data: "employee" },
      { data: "novelty_type" },
      { data: "description" },
      { data: "calendar_days" },
      { data: "business_days" },
      { data: "initial_date" },
      { data: "final_date" },
      {
        data: "action",
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

In HTML, you should have a structure similar to the following. Make sure that the number of columns defined in the JavaScript matches those defined in the HTML:

```html
<!-- Make sure you have the import of jQuery and the DataTable library -->

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