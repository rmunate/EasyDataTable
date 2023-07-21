# EasyDataTable: A fast, easy, and efficient way to create the BackEnd for any DataTable. (Laravel PHP Framework) | v1.x

![Logo](https://github.com/rmunate/EasyDataTable/assets/91748598/83e476be-25d4-4681-bc0f-264f2ed9a2a4)

[**----Documentación En Español----**](README_SPANISH.md)

## Table of Contents
- [Introduction](#introduction)
- [Installation](#installation)
- [Table Types](#table-types)
  - [ClientSide](#clientside)
  - [ServerSide](#serverside)
- [Client Side](#client-side)
  - [Route](#route)
  - [Controller](#controller)
  - [JavaScript](#javascript)
  - [HTML](#html)
- [Server Side](#server-side)
  - [Route](#route-1)
  - [Controller](#controller-1)
  - [JavaScript](#javascript-1)
  - [HTML](#html-1)
- [Creator](#creator)
- [License](#license)

## Introduction
EasyDataTable was born out of the need to standardize the backend for different DataTables commonly used in our Laravel projects. This package offers a convenient way to work with the built-in **Query Builder** in the Laravel framework to generate tables quickly with all the capabilities required by [DataTables](https://datatables.net/).

## Installation
To install the package via **Composer**, run the following command:
```shell
composer require rmunate/easy-datatable
```

## Table Types

| Type | Description |
| ---- | ----------- |
| **ClientSide** | This type of table is used when we send all the data to the FrontEnd, and it is responsible for organizing, filtering, and generating any type of interactivity with the table. However, this type is not recommended for large volumes of data, as the client's experience may be affected while the library renders all the data, which could take a considerable amount of time. |
| **ServerSide** | This type of table is used to handle high volumes of data. It only loads a limited amount of data from the backend on each interaction with the table. Commonly, it renders data in chunks, for example, up to a maximum of 100 records, depending on the table's pagination settings on the FrontEnd. In conclusion, this type of table is highly recommended if you want to maintain a fast and efficient interaction with the application. |

## Client Side
Let's see how to create the backend for a ClientSide table.

### Route
Define a GET route without sending any arguments, similar to what is shown below. If you want to download the example, you can find it [here](src/Examples/ClientSide).

```php
Route::get('/module/datatable', [ModuleController::class, 'dataTable']);
```

### Controller
Now that we have the route, let's proceed to create the method in the corresponding controller. This method will always handle **Query Builder**. For now, using *Eloquent* is not possible, as we need to know the column names to be rendered on the FrontEnd, and Query Builder offers more convenient ways to standardize it.

```php
<?php

//Import the use of the library.
use Rmunate\EasyDatatable\EasyDataTable;

//...

/* 
In the Request, you can send conditions for the query if required; otherwise, you can omit it. 
*/
public function dataTable(Request $request)
{
    /*
    The first thing we will do is create our query using Query Builder. 
    An important step is to use the "select" method, where you define the columns to select. 
    You can assign a different alias to the column name of the database table if you wish. 
    Below is an example of how to generate a query with some relationships. 
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
            )
            ->where('empleados.empresa', $request->company); /* (Optional) Only if you need to apply your conditions */

    /*
    (Optional)
    Sometimes we need to send additional information, such as permissions, to determine if row values can be altered. 
    In such cases, we can create variables with the additional data we want to send to the front end. 
    In the current example, I will only check if the logged-in user has edit permissions. 
    */
    $permissionEdit = Auth::user()->can('novedades.editar');

    /*
    Now let's start using the library. The first thing we'll do is create an object with an instance of EasyDataTable.
    */
    $datatable = new EasyDataTable();
    
    /*
    Now we'll define that we want to create the data for a "ClientSide" type.
    */
    $datatable->clientSide();
    
    /*
    Next, using the "query" method, we'll send the QueryBuilder query, which, as you can see, does not have any final methods. 
    You would commonly use "get" to retrieve data, but in this case, you should not use it; instead, send the QueryBuilder instance to the library.
    */
    $datatable->query($query);
    
    /*
    (Optional)
    The "map" method is not mandatory; you can omit it if you want to render the data on the front end exactly as it is returned from the database. 
    However, if you want to apply specific formats and add columns or data, you can do something like this.
    */
    $datatable->map(function($row) use ($permissionEdit){
        /*
        Note that within the "map" method, the "$row" alias represents the treatment of each line of the table to be returned. 
        Additionally, through the "use" statement, you can pass additional variables to the library's context, 
        which you need for data treatment or to add them as additional columns. 
        
        As you can see, the variable "$row" allows us to access each of the aliases created in our query. 
        
        It is essential that the array indices to be returned in this method match the aliases used in the QueryBuilder query. 
        If you notice, only the additional columns to be returned have names that are not consistent with the aliases set in the QueryBuilder query. 
        */
        return [
            'identification' => $row->identification,
            'employee'       => strtolower($row->employee),
            'novelty_type'   => strtolower($row->novelty_type),
            'description'    => strtolower($row->description),
            'calendar_days'  => $row->calendar_days,
            'business_days

'  => $row->business_days,
            'initial_date'   => date('d/m/Y', strtotime($row->initial_date)),
            'final_date'     => date('d/m/Y', strtotime($row->final_date)),
            "action" => [
                "editar" => $permissionEdit
            ]
        ];
    });

    /*
    Finally, using the "response" method, you'll get the response required by the FrontEnd to render the data.
    */
    return $datatable->response();

}
```

### JavaScript
Below is a basic example of DataTable configuration for the FrontEnd. Here, it's a matter of using what DataTable offers as a JavaScript library.

```javascript
// First, we need to initialize the table with the DataTable() function.
// The selector '#datatable' should be the ID or class of the table in the HTML.
var dataTable = $('#datatable').DataTable({

    processing: true, // Enable processing indicator
    responsive: true, // Enable responsive design functionality
    pagingType: "full_numbers", // Show all pagination controls

    /* Here, you have two options to get the data for the table: */

    // OPTION 1: Save the backend response in a variable and use the "data" property to pass the values to the DataTable.
    // data: dataBackEnd,

    // OPTION 2: Use the Ajax property to get the data from a URL on the backend.
    ajax: {
        url: baseUrl + '/module/datatable', // Change the URL that returns data in JSON format here
        dataSrc: 'data' // Specify the property that contains the data in the JSON response
    },

    /* Next, we define the table columns and the data we want to display in each column. */
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
            /* The "render" method allows you to customize how the content of a column is displayed. */
            render: function (data, type, row, meta) {
                let btnEdit = '';

                // In the current example, we validate the edit permission to render a button with the edit action.
                if (data.editar) {
                    btnEdit = `<button class="btn btn-sm btn-info btn-edit" data-id="${row.identification}" data-employee="${row.employee}" title="Edit">
                                    <i class="fa flaticon-edit-1"></i>
                                </button>`;
                }

                return `<div class='btn-group'>${btnEdit}</div>`;
            },
            orderable: false // Specify if the column is sortable or not.
        }
    ],

    /* Finally, we configure the language of the table using the corresponding translation file. */
    language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json"
    }
});
```

#### HTML
In the HTML, you should have a structure similar to the following. Make sure that the number of columns defined in the JavaScript matches the ones defined in the HTML:

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
Now let's see how to create the backend for a ServerSide table. You will notice many parts are similar to the previous example.

### Route
Define a GET route without sending any arguments, similar to what is shown below. You can download the example [here](src/Examples/ServerSide).

```php
Route::get('/module/datatable', [ModuleController::class, 'dataTable']);
```

### Controller
Now that we have the route, let's proceed to create the method in the corresponding controller. This method will always handle **Query Builder**. For now, using *Eloquent* is not possible, as we need to know the column names to be rendered on the FrontEnd, and Query Builder offers more convenient ways to standardize it.

```php
<?php

use Rmunate\EasyDatatable\EasyDataTable;

//...
public function dataTable(Request $request)
{
    /*
    The first thing we will do is create our query using Query Builder. An essential step is to use the "select" method, 
    where you define the columns to select. You can assign a different alias to the column name of the database table if you wish. 
    Below is an example of how to generate a query with some relationships. 
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

    /*
    (Optional)
    Sometimes we need to send additional information, such as permissions, to determine if row values can be altered. 
    In such cases, we can create variables with the additional data we want to send to the front end. 
    In the current example, I will only check if the logged-in user has edit permissions. 
    */
    $permissionEdit = Auth::user()->can('novedades.edit

ar');

    /*
    Now we will start using the library. The first thing we will do is create an object with an instance of EasyDataTable.
    */
    $datatable = new EasyDataTable();
    
    /*
    Now we will define that we want to create data for a "ServerSide" type.
    */
    $datatable->serverSide();

    /*
    For this type of table, it is necessary to pass the Request to the EasyDataTable instance as follows.
     */
    $datatable->request($request);

    /*
    Next, using the "query" method, we will send the QueryBuilder query. As you can see, it does not have any final methods. 
    Normally, you would use "get", but in this case, you should not use it; you must send the QueryBuilder instance to the library.
    */
    $datatable->query($query);

    /*
    (Optional)
    The "map" method is not mandatory; you can omit it if you want to render the data in the front end exactly as it is returned from the database. 
    Otherwise, if you want to format specific data, and add additional columns or data, you can do something like the following.
    */
    $datatable->map(function($row) use($editar){
        /*
        Within the "map" method, you will have an alias "$row" representing the treatment for each line of the table to be returned. 
        Additionally, through the "use" keyword, you can pass additional variables to the context of the EasyDataTable class, 
        which you may need for data processing or for adding them as additional columns.
        
        As you can see, the "$row" variable allows us to access each of the aliases created in our query. 
        
        It is essential that the indexes of the array to be returned in this method match the aliases in the QueryBuilder query. 
        If you notice, only the additional columns to be returned have names that do not match the aliases used in the QueryBuilder query. 
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
            "action" => [
                "editar" => $editar
            ]
        ];
    });

    /*
    This type of table commonly comes with a search feature, which you can configure from the "search" method. 
    Here, you can create a QueryBuilder closure where you apply your conditionals. Below is an example.
     */
    $datatable->search(function($query, $search){
        /*
        If you need to use any additional variables, remember that you can add the "use()" clause, 
        where you can pass variables to the context of the EasyDataTable class.
        */
        return $query->where(function($query) use ($search) {
                    $query->where('novedades.id', 'like', "%{$search}%")
                        ->orWhere('novedades.descripcion', 'like', "%{$search}%")
                        ->orWhere('tipo_novedades.nombre', 'like', "%{$search}%")
                        ->orWhere('empleados.nombre', 'like', "%{$search}%")
                        ->orWhere('empleados.cedula', 'like', "%{$search}%");
                });
    });

    /*
    Finally, using the "response" method, you'll get the response required by the FrontEnd to render the data.
    */
    return $datatable->response();

}
```

### JavaScript
Below is a basic example of DataTable configuration for the FrontEnd. Here, it's a matter of using what DataTable offers as a JavaScript library.

```javascript
// First, we need to initialize the table with the DataTable() function.
// The selector '#datatable' should be the ID or class of the table in the HTML.
var dataTable = $('#datatable').DataTable({
    processing: true, // Enable processing indicator
    responsive: true, // Enable responsive design functionality
    serverSide: true, // Enable ServerSide
    pagingType: "full_numbers", // Show all pagination controls
    ajax: { // ServerSide Ajax request
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
            /* The "render" method allows you to customize how the content of a column is displayed. */
            render: function (data, type, row, meta) {
                let btnEdit = '';

                // In the current example, we validate the edit permission to render a button with the edit action.
                if (data.editar) {
                    btnEdit = `<button class="btn btn-sm btn-info btn-edit" data-id="${row.identification}" data-employee="${row.employee}" title="Edit">
                                    <i class="fa flaticon-edit-1"></i>
                                </button>`;
                }

                return `<div class='btn-group'>${btnEdit}</div>`;
            },
            orderable: false // Specify if the column is sortable or not.
        }
    ],

    /* Finally, configure the language of the table using the corresponding translation file. */
    language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json"
    }
});
```

### HTML
In the HTML, you should have a structure similar to the following. Make sure that the number of columns defined in the JavaScript matches the ones defined in the HTML:

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

## Creator
- 🇨🇴 Raúl Mauricio Uñate Castro
- Email: raulmauriciounate@gmail.com

## License
This project is under the [MIT License](https://choosealicense.com/licenses/mit/).