# EasyDataTable: A Fast and Efficient Way to Create BackEnd for Any DataTable in the Laravel PHP Framework (v1.x)

⚙️ This library is compatible with Laravel versions +8.0 and also provides support for the Metronic theme in its versions +8.0 ⚙️

[![Laravel 8.0+](https://img.shields.io/badge/Laravel-8.0%2B-orange.svg)](https://laravel.com)
[![Laravel 9.0+](https://img.shields.io/badge/Laravel-9.0%2B-orange.svg)](https://laravel.com)
[![Laravel 10.0+](https://img.shields.io/badge/Laravel-10.0%2B-orange.svg)](https://laravel.com)

![Logo](https://github.com/rmunate/EasyDataTable/assets/91748598/326fc805-ba79-478c-b686-3214d622a987)

📖 [**DOCUMENTACIÓN EN ESPAÑOL**](README_SPANISH.md) 📖

## Table of Contents
- [Introduction](#introduction)
- [Installation](#installation)
- [Table Types](#table-types)
  - [ClientSide](#clientside)
  - [ServerSide](#serverside)
- [Usage](#usage)
  - [Client-Side Configuration](#client-side-configuration)
    - [Route](#route)
    - [Controller](#controller)
    - [JavaScript](#javascript)
    - [HTML](#html)
  - [Server-Side Configuration](#server-side-configuration)
    - [Route](#route-1)
    - [Controller](#controller-1)
    - [JavaScript](#javascript-1)
    - [HTML](#html-1)
- [Creator](#creator)
- [License](#license)

## Introduction
EasyDataTable arises from the need to standardize the backend for different DataTables commonly used in Laravel projects. This package offers a convenient way to work with Laravel's built-in Query Builder to generate tables quickly and easily with all the capabilities required by [DataTables](https://datatables.net/).

It also provides support for DataTables in Metronic themes.

[![Metronic 8.0+](https://preview.keenthemes.com/html/metronic/docs/assets/media/logos/metronic.svg)](https://keenthemes.com/metronic)

## Installation
To install the dependency via Composer, run the following command:

```shell
composer require rmunate/easy-datatable
```

## Table Types
Below, we detail the difference between the two types of tables that you can implement in your project, depending on the amount of data you are working with.

### ClientSide
The main feature of a ClientSide DataTable is that all data and data manipulation logic are handled on the client side, i.e., in the user's web browser, rather than making requests to the web server for each interaction. This means that all the data needed to populate the table is initially loaded in the user's browser, and operations such as search, filtering, sorting, and pagination are performed without the need to communicate with the server.

### ServerSide
The main feature of a ServerSide DataTable is that most data-related operations are performed on the server side rather than the client side. Here are some key aspects of a ServerSide DataTable:

**Efficient Data Loading:** In a ServerSide DataTable, data is loaded from the server as the user navigates the table or performs actions such as search, filtering, sorting, and pagination. This allows for handling large data sets without overloading the user's browser.

**Server Requests:** When an action that requires data manipulation, such as sorting the table, is performed, the DataTable sends a request to the web server. The server processes the request and returns the corresponding data.

**Efficient Filtering and Searching:** Filtering and searching are performed on the server side, which means that only relevant data is sent to the client. This is especially useful when working with large amounts of data.

**Security:** By performing most operations on the server, you can implement greater security and authorization control to ensure that users only access data they have permission to access.

## Usage

### Client-Side Configuration
If you want to download the example, you can find it [here](Examples/ClientSide).

Let's see how to create the backend for a ClientSide DataTable. Remember that in these cases, all the information must be delivered to the FrontEnd, and it is not recommended for very large data sizes.

#### Route
Define a GET route without sending arguments in the URL; this will be done implicitly by the DataTable JS library.

```php
Route::get('/module/datatable', [NameController::class, 'dataTable']);
```

#### Controller
Now that we have the route, we will create the method in the corresponding controller. This method will always use Query Builder. For now, it is not possible to use Eloquent because it requires knowing the names of the columns to be rendered in the FrontEnd, and Query Builder provides more convenient ways to standardize this.

```php
use Rmunate\EasyDatatable\EasyDataTable;

class NameController extends Controller
{
    /**
     * Define a method that handles the GET route receiving a Request variable.
     */
    public function dataTable(Request $request)
    {
        /**
         * Create a query using Query Builder; you can apply any conditions you need, just do NOT apply the final get() method.
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
         * (Optional) Sometimes, to determine if a user has permissions for some action on the table rows, you need to make queries like these.
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
             * (Optional) If you need to customize how the information is displayed in the table, the map() method will be of great help.
             * Additionally, if you need to send additional data or perform validations, you can apply the logic here.
             * It's important that the alias of each value in the query matches the value used in the array, as shown below.
             */
            return [
                'identification' => $row->identification,
                'employee' => strtolower($row->employee),
                'novelty_type' => strtolower($row->novelty_type),
                'description' => strtolower($row->description),
                'calendar_days' => $row->calendar_days,
                'business_days' => $row->business_days,
                'initial_date' => date('d/m/Y', strtotime($row->initial_date)),
                'final_date' => date('d/m/Y', strtotime($row->final_date)),
                'action' => [
                    'editar' => $permissionEdit,
                ],
            ];
        });

        /**
         * This method returns the constructed structure as required by the DataTable library in the FrontEnd.
         */
        return $datatable->response();
    }
}
```

#### JavaScript
Below is a basic example of DataTable configuration. Here, you can use what you need from what DataTable offers as a JS library with JQuery. In this example, you will find what we consider necessary to create a table that covers the standard needs of a web application.

```javascript
/* You will create a global variable in the JS file to store the value of the data table to be built. This will be very useful when you need to update the table information without having to reload the page. */
var dataTable;

/* Define this variable that will contain the server base where requests will be sent */
var baseUrl = window.location.protocol + "//" + window.location.host

/* When the file is loaded */
$(document).ready(function () {
  /* Here, we will assign an instance of dataTable to the variable; make sure it has the same ID as the table tag. */
  dataTable = $("#datatable").DataTable({
    processing: true,
    responsive: true,
    pagingType: "full_numbers",
    ajax: {
      /* Make sure the route (web.php) is of type GET */
      url: baseUrl + "/module/datatable",
      dataSrc: "data",
    },
    columns: [
      /* The name must match the one from the associative array returned from the BackEnd */
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
    /* Remember that you can apply the language settings for your region. */
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json",
    },
  });
});
```

#### HTML
In the HTML, you should have a structure similar to the one below. Make sure that the number of columns defined in JavaScript matches the number defined in the HTML:

```html
<!-- Make sure to import jQuery and the DataTable library -->

<!-- You should create the table structure and assign it an ID that will be used as a selector to turn it into a DataTable. Also, make sure that the headers match the number of columns configured in JavaScript. -->
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

### Server-Side Configuration
If you want to download the example, you can find it [here](Examples/ServerSide).

Below, we explain how to configure the backend for a ServerSide table:

#### Route
You need to define a GET route without sending arguments in the URL; this will be done implicitly by the DataTable JS library.

```php
Route::get('/module/datatable', [NameController::class, 'dataTable']);
```

#### Controller
Now that we have the route, we will create the method in the corresponding controller. In this case, the method will handle Query Builder. It is not possible to use Eloquent because it requires knowing the names of the columns to be rendered in the FrontEnd, and Query Builder provides more convenient ways to standardize this.

```php
use Rmunate\EasyDatatable\EasyDataTable;

class NameController extends Controller
{
    /**
     * Define a method that handles the GET route receiving a Request variable.
     */
    public function dataTable(Request $request)
    {
        /**
         * Create a query using Query Builder; you can apply any conditions you need, just do NOT apply the final get() method.
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
         * (Optional) Sometimes, to determine if a user has permissions for some action on the table rows, you need to make queries like these.
         */
        $permissionEdit = Auth::user()->can('novedades.editar');

        /**
         * Using the library is as simple as using the following code.
         */
        $datatable = new EasyDataTable();
        $datatable->serverSide(); /* Mandatory / Required */
        $datatable->query($query);
        $datatable->map(function ($row) use ($permissionEdit) {
            /**
             * (Optional) If you need to customize how the information is displayed in the table, the map() method will be of great help.
             * Additionally, if you need to send additional data or perform validations, you can apply the logic here.
             * It's important that the alias of each value in the query matches the value used in the array, as shown below.
             */
            return [
                'identification' => $row->identification,
                'employee' => strtolower($row->employee),
                'novelty_type' => strtolower($row->novelty_type),
                'description' => strtolower($row->description),
                'calendar_days' => $row->calendar_days,
                'business_days' => $row->business_days,
                'initial_date' => date('d/m/Y', strtotime($row->initial_date)),
                'final_date' => date('d/m/Y', strtotime($row->final_date)),
                'action' => [
                    'editar' => $permissionEdit,
                ],
            ];
        });

        /**
         * This method returns the constructed structure as required by the DataTable library in the FrontEnd.
         */
        return $datatable->response();
    }
}
```

#### JavaScript
Below is a basic example of DataTable configuration. Here, you can use what you need from what DataTable offers as a JS library with JQuery. In this example, you will find what we consider necessary to create a table that covers the standard needs of a web application.

```javascript
/* You will create a global variable in the JS file to store the value of the data table to be built. This will be very useful when you need to update the table information without having to reload the page. */
var dataTable;

/* Define this variable that will contain the server base where requests will be sent */
var baseUrl = window.location.protocol + "//" + window.location.host

/* When the file is loaded */
$(document).ready(function () {
  /* Here, we will assign an instance of dataTable to the variable; make sure it has the same ID as the table tag. */
  dataTable = $("#datatable").DataTable({
    processing: true,
    serverSide: true, /* Note, this value must be true to enable communication with the server */
    responsive: true,
    pagingType: "full_numbers",
    ajax: {
      /* Make sure the route (web.php) is of type GET */
      url: baseUrl + "/module/datatable",
      dataSrc: "data",
    },
    columns: [
      /* The name must be the same as the one in the associative array returned from the BackEnd */
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
    /* Remember that you can apply the language settings for your region. */
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json",
    },
  });
});
```

#### HTML
In the HTML, you should have a structure similar to the one below. Make sure that the number of columns defined in JavaScript matches the number defined in the HTML:

```html
<!-- Make sure to import jQuery and the DataTable library -->

<!-- You should create the table structure and assign it an ID that will be used as a selector to turn it into a DataTable. Also, make sure that the headers match the number of columns configured in JavaScript. -->
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

🌟 Support My Projects! 🚀

Make any contributions you see fit; the code is entirely yours. Together, we can do amazing things and improve the world of development. Your support is invaluable. ✨

If you have ideas, suggestions, or just want to collaborate, we are open to everything! Join our community and be part of our journey to success! 🌐👩‍💻👨‍💻