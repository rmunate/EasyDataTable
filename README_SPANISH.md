# EasyDataTable, una forma rápida, fácil y eficiente de crear el BackEnd para cualquier DataTable. (Laravel PHP Framework) | v1.x
⚙️ Esta librería es compatible con versiones de Laravel 8.0 y superiores ⚙️

[![Laravel 8.0+](https://img.shields.io/badge/Laravel-8.0%2B-orange.svg)](https://laravel.com)
[![Laravel 9.0+](https://img.shields.io/badge/Laravel-9.0%2B-orange.svg)](https://laravel.com)
[![Laravel 10.0+](https://img.shields.io/badge/Laravel-10.0%2B-orange.svg)](https://laravel.com)

![Logo](https://github.com/rmunate/EasyDataTable/assets/91748598/83e476be-25d4-4681-bc0f-264f2ed9a2a4)

[**----Documentation In English----**](README.md)

## Tabla de Contenido
- [Introducción](#introducción)
- [Instalación](#instalación)
- [Tipos De Tablas](#tipos-de-tablas)
  - [ClientSide](#client-side)
  - [ServerSide](#server-side)
- [Client Side](#client-side-1)
  - [Ruta](#ruta)
  - [Controlador](#controlador)
  - [JavaScript](#javascript)
  - [HTML](#html)
- [Server Side](#server-side-1)
  - [Ruta](#ruta-1)
  - [Controlador](#controlador-1)
  - [JavaScript](#javascript-1)
  - [HTML](#html-1)
- [Creador](#creador)
- [Licencia](#licencia)

## Introducción
EasyDataTable, nace de la necesidad de estandarizar el backend para las diferentes datatables que solemos manejar en nuestros proyectos **Laravel**, este paquete ofrece una manera conveniente de trabajar con **Query Builder** incorporado en el Marco de Laravel, para generar fácil y rápido tablas con todas las capacidades que requiere [DataTables](https://datatables.net/ "DataTables")

## Instalación
Para instalar la dependencia a través de **composer**, ejecuta el siguiente comando:
```shell
composer require rmunate/easy-datatable
```

## Tipos De Tablas

|  Tipo | Descripción  |
| ------------ | ------------ |
| **ClientSide** | Este tipo de tabla es cuando le enviamos el total de los datos al Front y es este quien se encarga de organizar, filtrar y generar cualquier tipo de interactividad con la tabla, este tipo de tabla no se recomienda usar con altos volúmenes de datos, ya que la experiencia al cliente se vería afectada mientras la librería renderiza el total de datos puede tomar una cantidad de tiempo considerable. |
| **ServerSide** | Este tipo de tabla se usa para manejar una alta demanda de datos, en si lo que hace es que no carga el total de la data, sino que en cada interacción con la tabla, esta solicita una cantidad limitada de valores al backend para renderizar, comúnmente pintando la data de a máximo 100 registros, esto dependiendo de la lista desplegable de la tabla en el front donde se define la cantidad de registros a mostrar por página, en conclusión es el tipo de tabla más recomendada si se desea mantener una interacción rápida y eficiente con la aplicación. |

## Client Side
Veamos cómo se crea el backend para una tabla ClientSide.

### Ruta
Define una ruta tipo GET sin envio de argumentos, algo similar a lo que te muestro a continuación. Si deseas descargar el ejemplo, en esta ruta lo encuentras [Descargar Ejemplo](src/Examples/ClientSide)

```php
Route::get('/modulo/datatable', [ModuloController::class, 'dataTable']);
```

### Controlador
Ahora que ya tenemos la ruta, procederemos a crear el método en el controlador correspondiente. Este método siempre manejará **Query Builder**. Por ahora no es posible usar *Eloquent*, esto debido a que se requiere conocer el nombre de las columnas que se desean renderizar en el Front y Query Builder nos ofrecer maneras más convenientes de estandarizarlo.

```php
<?php

//Importar uso de la libreria.
use Rmunate\EasyDatatable\EasyDataTable;

//...

/* 
En el Request podrás enviar condiciones para la consulta si así lo requieres de lo contrario omítelo. 
*/
public function dataTable(Request $request)
{
    /*
    Lo primero que haremos es crear nuestra consulta a través de Query Builder, 
    algo importante es que debes usar el método "select", donde definas las columnas a seleccionar, 
    puedes, si así lo deseas, asignar un alias diferente al nombre de la columna de la tabla de la base de datos, 
    a continuación te dejo un ejemplo como generar una consulta con algunas relaciones. 
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
            ->where('empleados.empresa', $request->company); /* (Opcional) Solo si requieres aplicar tus condiciones */

    /*
    (Opcional)
    Algunas veces debemos enviar información adicional como permisos para determinar si se pueden alterar los valores de una fila,
    en esos casos, podemos crear variables con la data adicional que deseamos enviar al front, 
    en el ejemplo actual solo validaré si el usuario en sesión tiene permisos de edición. 
    */
    $permissionEdit = Auth::user()->can('novedades.editar');

    /*
    Ahora iniciaremos a usar la librería, lo primero que haremos será crear un objeto con una instancia de EasyDataTable.
    */
    $datatable = new EasyDataTable();
    
    /*
    Ahora definiremos que buscamos crear la data para un tipo "Clientside".
    */
    $datatable->clientSide();
    
    /*
    Luego, usando el método "query", enviaremos la consulta de QueryBuilder, la cual si te fijas, no cuenta con ningún método final, 
    comúnmente usarías "get", pero en este caso no debes usarlo, debes enviar la instancia de QueryBuilder a la librería.
    */
    $datatable->query($query);
    
    /*
    (Opcional)
    El método "map" no es obligatorio, puedes omitirlo si buscas renderizar la data en el front tal cual la retorne la base de datos, 
    de lo contrario, si buscas dar formatos específicos, y adicionar columnas o datos, podrás hacer algo como lo siguiente.
    */
    $datatable->map(function($row) use($permissionEdit){
        /*
        A tener en cuenta, dentro del método "map" tendrás un alias "$row" que representa el tratamiento a cada línea de la tabla a retornar,
        adicional a través de "use" podrás pasar al contexto de la librería las variables adicionales 
        que requieras para hacer tratamiento a los datos o para añadirlas como columnas adicionales.
        
        Como lo notarás, la variable "$row", nos permitirá acceder a cada uno de los alias creados en nuestra consulta. 
        
        Algo que es obligatorio es que los índices del arreglo a retornar en este método sean iguales a los alias de la consulta a la base de datos.
        Si te fijas, solo las columnas adicionales a retornar tienen nombres que no son consecuentes con los alias puestos a la consulta en QueryBuilder. 
        */
        return [
            'identification' => $row->identification, /*  */
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

    /*
    Finalmente, usando el método "response" tendrás la respuesta que requiere el Front para renderizar los datos.
    */
    return $datatable->response();

}
```

### JavaScript
A continuación un ejemplo básico de configuración de la DataTable, aquí ya es cuestión de que emplees lo que requieras de lo que ofrece DataTable como libreria de JS. 

```javascript
// Primero, debemos inicializar la tabla con la función DataTable().
// El selector '#datatable' debe ser el ID o clase de la tabla en el HTML.
var dataTable = $('#datatable').DataTable({

    processing: true, // Habilitar el indicador de procesamiento
    responsive: true, // Habilitar la funcionalidad de diseño responsivo
    pagingType: "full_numbers", // Mostrar todos los controles de paginación

    /* Aquí tienes dos opciones para obtener los datos de la tabla: */

    // OPCION 1: Guardar la respuesta del backend en una variable y usar la propiedad "data" para pasar los valores a la datatable.
    // data: dataBackEnd,

    // OPCION 2: Usar la propiedad Ajax para obtener los datos desde una URL en el backend.
    ajax: {
        url: baseUrl + '/modulo/datatable', // Cambiar aquí la URL que retorna los datos en formato JSON
        dataSrc: 'data' // Indicar la propiedad que contiene los datos en la respuesta JSON
    },

    /* A continuación, definimos las columnas de la tabla y los datos que queremos mostrar en cada columna. */
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
            /* El método "render" permite personalizar cómo se mostrará el contenido de una columna. */
            render: function (data, type, row, meta) {
                let btnEdit = '';

                // En el ejemplo actual, se valida el permiso de editar para renderizar un botón con la acción de editar.
                if (data.editar) {
                    btnEdit = `<button class="btn btn-sm btn-info btn-edit" data-id="${row.identification}" data-employee="${row.employee}" title="Edit">
                                    <i class="fa flaticon-edit-1"></i>
                                </button>`;
                }

                return `<div class='btn-group'>${btnEdit}</div>`;
            },
            orderable: false // Indicar si la columna es ordenable o no.
        }
    ],

    /* Finalmente, configuramos el idioma de la tabla utilizando el archivo de traducción correspondiente. */
    language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json"
    }
});
```

#### HTML
En el HTML deberas contar con una estructura similar a la siguiente, recuerda garantizar que concuerden la cantidad de columnas definidas en el JS con las defindas en el HTML:

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
            <th>Ccalendar Days</th>
            <th>Business Days</th>
            <th>Initial Date</th>
            <th>Final Date</th>
            <th>Action</th>
        </tr>
    </thead>
</table>
```

## Server Side
Veamos cómo ahora como se crea el backend para una tabla ServerSide.
Vas a ver muchas partes similares al anterior ejemplo:

#### Ruta
Define una ruta tipo GET sin envio de argumentos, algo simular a lo que te muestro a continuación.  [Descargar Ejemplo](src/Examples/ServerSide)

```php
Route::get('/modulo/datatable', [ModuloController::class, 'dataTable']);
```

2. Controlador: Ahora que ya tenemos la ruta, procederemos a crear el método en el controlador correspondiente. Este método siempre manejará **Query Builder**. Por ahora no es posible usar *Eloquent*, esto debido a que se requiere conocer el nombre de las columnas que se desean renderizar en el Front y Query Builder nos ofrecer maneras más convenientes de estandarizarlo.

```php
<?php

use Rmunate\EasyDatatable\EasyDataTable;

//...
public function dataTable(Request $request)
{
    /*
    Lo primero que haremos es crear nuestra consulta a través de Query Builder, algo obligatorio es que debes usar el método "select", 
    donde definas las columnas a seleccionar, puedes, si así lo deseas, asignar un alias diferente al nombre de la columna de la tabla de la base de datos, 
    a continuación te dejo un ejemplo como generar una consulta con algunas relaciones. 
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
    (Opcional)
    Algunas veces debemos enviar información adicional como permisos para determinar si se pueden alterar los valores de una fila, 
    en esos casos, podemos crear variables con la data adicional que deseamos enviar al front, 
    en el ejemplo actual solo validaré si el usuario en sesión tiene permisos de edición. 
    */
    $permissionEdit = Auth::user()->can('novedades.editar');

    /*
    Ahora iniciaremos a usar la librería, lo primero que haremos será crear un objeto con una instancia de EasyDataTable.
    */
    $datatable = new EasyDataTable();
    
    /*
    Ahora definiremos que buscamos crear la data para un tipo "ServerSide".
    */
    $datatable->serverSide();

    /*
    Para este tipo de Tablas, es necesario pasarle el Request a la instancia EasyDataTable. De la siguiente manera.
     */
    $datatable->request($request);

    /*
    Luego, usando el método "query", enviaremos la consulta de QueryBuilder, la cual si te fijas, 
    no cuenta con ningún método final, comúnmente usarías "get", pero en este caso no debes usarlo, 
    debes enviar la instancia de QueryBuilder a la librería.
    */
    $datatable->query($query);

    /*
    (Opcional)
    El método "map" no es obligatorio, puedes omitirlo si buscas renderizar la data en el front tal cual la retorne la base de datos, 
    de lo contrario, si buscas dar formatos específicos, y adicionar columnas o datos, podrás hacer algo como lo siguiente.
    */
    $datatable->map(function($row) use($editar){
        /*
        A tener en cuenta, dentro del método "map" tendrás un alias "$row" que representa el tratamiento a cada línea de la tabla a retornar, 
        adicional a través de "use" podrás pasar al contexto de la librería las variables adicionales que requieras para hacer tratamiento 
        a los datos o para añadirlas como columnas adicionales. Como lo notarás, la variable "$row", nos permitirá acceder a cada uno 
        de los alias creados en nuestra consulta. Algo que es obligatorio es que los índices del arreglo a retornar en este método 
        sean iguales a los alias de la consulta a la base de datos. Si te fijas, solo las columnas adicionales a retornar tienen 
        nombres que no son consecuentes con los alias puestos a la consulta en QueryBuilder. 
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
    Este tipo de tablas, comunmente van acompañadas de un buscador al cual le podremos indicar como filtrar la data desde el metodo 
    "search" aquí podras crear un clousure de QueryBuilder donde apliques tus condicionales. a continuacion un ejemplo.
     */
    $datatable->search(function($query, $search){
        /*
        Si necesitas usar alguna variable adicional, recuarda que puedas agregar la clausula "use()" 
        donde puedes pasar variables al contexto de la clase EasyDataTable
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
    Finalmente, usando el método "response" tendrás la respuesta que requiere el Front para renderizar los datos.
    */
    return $datatable->response();

}
```

#### JavaScript
A continuación un ejemplo básico de configuración de la DataTable, aquí ya es cuestión de que emplees lo que requieras de lo que ofrece DataTable. 

```javascript
// Primero, debemos inicializar la tabla con la función DataTable().
// El selector '#datatable' debe ser el ID o clase de la tabla en el HTML.
var dataTable = $('#datatable').DataTable({
    processing: true, // Habilitar el indicador de procesamiento
    responsive: true, // Habilitar la funcionalidad de diseño responsivo
    serverSide: true, // Hsbilitar ServerSide
    pagingType: "full_numbers", // Mostrar todos los controles de paginación
    ajax: { //Peticion Ajax ServerSide
        url: baseUrl + "/modulo/datatable",
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
            /* El método "render" permite personalizar cómo se mostrará el contenido de una columna. */
            render: function (data, type, row, meta) {
                let btnEdit = '';

                // En el ejemplo actual, se valida el permiso de editar para renderizar un botón con la acción de editar.
                if (data.editar) {
                    btnEdit = `<button class="btn btn-sm btn-info btn-edit" data-id="${row.identification}" data-employee="${row.employee}" title="Edit">
                                    <i class="fa flaticon-edit-1"></i>
                                </button>`;
                }

                return `<div class='btn-group'>${btnEdit}</div>`;
            },
            orderable: false // Indicar si la columna es ordenable o no.
        }
    ],

    /* Finalmente, configuramos el idioma de la tabla utilizando el archivo de traducción correspondiente. */
    language: {
        url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json"
    }
});
```

#### HTML
En el HTML deberas contar con una estructura similar a la siguiente, recuerda garantizar que concuerden la cantidad de columnas definidas en el JS con las defindas en el HTML:

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
            <th>Ccalendar Days</th>
            <th>Business Days</th>
            <th>Initial Date</th>
            <th>Final Date</th>
            <th>Action</th>
        </tr>
    </thead>
</table>
```

## Creador
- 🇨🇴 Raúl Mauricio Uñate Castro
- Correo electrónico: raulmauriciounate@gmail.com

## Licencia
Este proyecto se encuentra bajo la [Licencia MIT](https://choosealicense.com/licenses/mit/).