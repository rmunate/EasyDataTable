# EasyDataTable: Una forma rápida y eficiente de crear el BackEnd para cualquier DataTable en el Framework PHP Laravel (v1.x)

⚙️ Esta librería es compatible con versiones de Laravel +8.0 y también cuenta con soporte para el tema Metronic en sus versiones +8.0 ⚙️

[![Laravel 8.0+](https://img.shields.io/badge/Laravel-8.0%2B-orange.svg)](https://laravel.com)
[![Laravel 9.0+](https://img.shields.io/badge/Laravel-9.0%2B-orange.svg)](https://laravel.com)
[![Laravel 10.0+](https://img.shields.io/badge/Laravel-10.0%2B-orange.svg)](https://laravel.com)

![Logo-easy-datatable](https://github.com/alejandrodiazpinilla/EasyDataTable/assets/51100789/403b944a-1991-4ebc-864c-762567848fc7)

📖 [**DOCUMENTACIÓN EN INGLÉS**](README.md) 📖

## Tabla de Contenido
- [Introducción](#introducción)
- [Instalación](#instalación)
- [Tipos De Tablas](#tipos-de-tablas)
  - [ClientSide](#clientside)
  - [ServerSide](#serverside)
- [Uso](#uso)
  - [Configuración Del Lado Del Cliente](#configuración-del-lado-del-cliente)
    - [Ruta](#ruta)
    - [Controlador](#controlador)
    - [JavaScript](#javascript)
    - [HTML](#html)
  - [Configuración Del Lado Del Servidor](#configuración-del-lado-del-servidor)
    - [Ruta](#ruta-1)
    - [Controlador](#controlador-1)
    - [JavaScript](#javascript-1)
    - [HTML](#html-1)
- [Creador](#creador)
- [Licencia](#licencia)

## Introducción
EasyDataTable surge de la necesidad de estandarizar el backend para las diferentes DataTables que se suelen manejar en proyectos Laravel. Este paquete ofrece una manera conveniente de trabajar con el Query Builder incorporado en el framework Laravel para generar tablas de manera rápida y sencilla con todas las capacidades requeridas por [DataTables](https://datatables.net/).

También ofrece soporte para DataTables en temas Metronic.

[![Metronic 8.0+](https://preview.keenthemes.com/html/metronic/docs/assets/media/logos/metronic.svg)](https://keenthemes.com/metronic)

## Instalación
Para instalar la dependencia a través de Composer, ejecuta el siguiente comando:

```shell
composer require rmunate/easy-datatable
```

## Tipos De Tablas
A continuación, se detalla la diferencia entre los dos tipos de tablas que puedes implementar en tu proyecto, dependiendo de la cantidad de datos con los que trabajes.

### ClientSide
La característica principal de una DataTable de tipo ClientSide es que todos los datos y la lógica de manipulación de datos se gestionan en el lado del cliente, es decir, en el navegador web del usuario, en lugar de hacer consultas al servidor web para cada interacción. Esto significa que todos los datos necesarios para llenar la tabla se cargan inicialmente en el navegador del usuario, y las operaciones de búsqueda, filtrado, ordenamiento y paginación se realizan sin necesidad de comunicarse con el servidor.

### ServerSide
La característica principal de una DataTable de tipo ServerSide es que la mayoría de las operaciones relacionadas con los datos se realizan en el lado del servidor en lugar del lado del cliente. Aquí hay algunos aspectos clave de una DataTable de tipo ServerSide:

**Carga Eficiente de Datos:** En una DataTable ServerSide, los datos se cargan desde el servidor a medida que el usuario navega por la tabla o realiza acciones como búsqueda, filtrado, ordenamiento y paginación. Esto permite gestionar grandes conjuntos de datos sin sobrecargar el navegador del usuario.

**Solicitudes al Servidor:** Cuando se realiza una acción que requiere manipulación de datos, como ordenar la tabla, la DataTable envía una solicitud al servidor web. El servidor procesa la solicitud y devuelve los datos correspondientes.

**Filtrado y Búsqueda Eficientes:** El filtrado y la búsqueda se realizan en el lado del servidor, lo que significa que solo se envían al cliente los datos relevantes. Esto es especialmente útil cuando se trabaja con grandes cantidades de datos.

**Seguridad:** Al realizar la mayoría de las operaciones en el servidor, se puede implementar un mayor control de seguridad y autorización para garantizar que los usuarios solo accedan a los datos a los que tienen permiso.

## Uso

### Configuración Del Lado Del Cliente
Si deseas descargar el ejemplo, puedes encontrarlo [aquí](Examples/ClientSide).

Veamos cómo se crea el backend para una tabla ClientSide. Recuerda que en estos casos, toda la información debe entregarse al Front y no es recomendable para tamaños de datos muy grandes.

#### Ruta
Define una ruta tipo GET sin enviar argumentos por la URL; esto lo hará de forma implícita la librería de JS DataTable.

```php
Route::get('/modulo/datatable', [NameController::class, 'dataTable']);
```

#### Controlador
Ahora que ya tenemos la ruta, procederemos a crear el método en el controlador correspondiente. Este método siempre manejará Query Builder. Por ahora, no es posible usar Eloquent, esto se debe a que se requiere conocer el nombre de las columnas que se desean renderizar en el Front y Query Builder nos ofrece maneras más convenientes de estandarizarlo.

```php
use Rmunate\EasyDatatable\EasyDataTable;

class NameController extends Controller
{
    /**
     * Define un método que maneje la ruta tipo GET recibiendo una variable de tipo Request.
     */
    public function dataTable(Request $request)
    {
        /**
         * Crea una consulta con Query Builder, puedes aplicar todas las condiciones que requieras, solo NO apliques el método final get().
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
         * (Opcional) A veces, para determinar si un usuario tiene permisos sobre alguna acción en las filas de la tabla, debes realizar consultas como estas.
         */
        $permissionEdit = Auth::user()->can('novedades.editar');

        /**
         * El uso de la librería es tan simple como emplear el siguiente código.
         */
        $datatable = new EasyDataTable();
        $datatable->clientSide(); /* Obligatorio / Requerido */
        $datatable->query($query);
        $datatable->map(function ($row) use ($permissionEdit) {
            /**
             * (Opcional) Si necesitas personalizar la forma en que se visualiza la información en la tabla, el método map() será de gran ayuda.
             * Además, si necesitas enviar datos adicionales o realizar validaciones, aquí puedes aplicar la lógica.
             * Es importante que el alias de cada valor en la consulta sea el mismo valor que se utiliza en el array, como se muestra a continuación.
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
         * Este método retorna la estructura construida tal como la requiere la librería DataTable en el Front.
         */
        return $datatable->response();
    }
}
```

#### JavaScript
A continuación, un ejemplo básico de configuración de la DataTable. Aquí ya es cuestión de que emplees lo que requieras de lo que ofrece DataTable como librería de JS con JQuery. En este ejemplo encontrarás lo que consideramos necesario para que puedas crear una tabla que cubra las necesidades estándar de un software web.

```javascript
/* Crearás una variable global en el archivo JS para almacenar el valor de la tabla de datos a construir. Esto te será de gran utilidad cuando necesites actualizar la información de la tabla sin tener que recargar la página. */
var dataTable;

/* Define esta variable que contendrá la base del servidor a donde se enviarán las peticiones */
var baseUrl = window.location.protocol + "//" + window.location.host

/* Al cargar el archivo */
$(document).ready(function () {
  /* Aquí le asignaremos a la variable una instancia de dataTable; asegura que sea el mismo ID del tag table */
  dataTable = $("#datatable").DataTable({
    processing: true,
    responsive: true,
    pagingType: "full_numbers",
    ajax: {
      /* Asegúrate de que la ruta (web.php) sea de tipo GET */
      url: baseUrl + "/modulo/datatable",
      dataSrc: "data",
    },
    columns: [
      /* El nombre debe ser el mismo que el del arreglo asociativo devuelto desde el BackEnd */
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
    /* Recuerda que puedes aplicar la configuración de idioma de tu región. */
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json",
    },
  });
});
```

#### HTML
En el HTML deberás contar con una estructura similar a la siguiente. Asegúrate de que coincidan la cantidad de columnas definidas en el JS con las definidas en el HTML:

```html
<!-- Asegúrate de tener la importación de jQuery y la biblioteca DataTable -->

<!-- Deberás crear la estructura de la tabla y asignarle un ID que se utilizará como selector para convertirla en una "DataTable". Además, asegúrate de que los encabezados coincidan con el número de columnas configurado en JavaScript. -->
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

### Configuración

 Del Lado Del Servidor
Si deseas descargar el ejemplo, puedes encontrarlo [aquí](Examples/ServerSide).

A continuación, se explica cómo configurar el backend para una tabla ServerSide:

#### Ruta
Debes definir una ruta tipo GET sin enviar argumentos por la URL; esto lo hará de forma implícita la librería JS DataTable.

```php
Route::get('/modulo/datatable', [NameController::class, 'dataTable']);
```

#### Controlador
Ahora que ya tenemos la ruta, procederemos a crear el método en el controlador correspondiente. En este caso, el método manejará Query Builder. No es posible usar Eloquent, esto se debe a que es necesario conocer el nombre de las columnas que se desean renderizar en el Front, y Query Builder ofrece maneras más convenientes de estandarizarlo.

```php
use Rmunate\EasyDatatable\EasyDataTable;

class NameController extends Controller
{
    /**
     * Define un método que maneje la ruta tipo GET recibiendo una variable de tipo Request.
     */
    public function dataTable(Request $request)
    {
        /**
         * Crea una consulta con Query Builder, puedes aplicar todas las condiciones que requieras, solo NO apliques el método final get().
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
         * (Opcional) A veces, para determinar si un usuario tiene permisos sobre alguna acción en las filas de la tabla, debes realizar consultas como estas.
         */
        $permissionEdit = Auth::user()->can('novedades.editar');

        /**
         * El uso de la librería es tan simple como emplear el siguiente código.
         */
        $datatable = new EasyDataTable();
        $datatable->serverSide(); /* Obligatorio / Requerido */
        $datatable->query($query);
        $datatable->map(function ($row) use ($permissionEdit) {
            /**
             * (Opcional) Si necesitas personalizar la forma en que se visualiza la información en la tabla, el método map() será de gran ayuda.
             * Además, si necesitas enviar datos adicionales o realizar validaciones, aquí puedes aplicar la lógica.
             * Es importante que el alias de cada valor en la consulta sea el mismo valor que se utiliza en el array, como se muestra a continuación.
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
         * Este método retorna la estructura construida tal como la requiere la librería DataTable en el Front.
         */
        return $datatable->response();
    }
}
```

#### JavaScript
A continuación, un ejemplo básico de configuración de DataTable. Aquí ya es cuestión de que emplees lo que requieras de lo que ofrece DataTable como librería de JS con JQuery. En este ejemplo encontrarás lo que consideramos necesario para que puedas crear una tabla que cubra las necesidades estándar de un software web.

```javascript
/* Crearás una variable global en el archivo JS para almacenar el valor de la tabla de datos a construir. Esto te será de gran utilidad cuando necesites actualizar la información de la tabla sin tener que recargar la página. */
var dataTable;

/* Define esta variable que contendrá la base del servidor a donde se enviarán las peticiones */
var baseUrl = window.location.protocol + "//" + window.location.host

/* Al cargar el archivo */
$(document).ready(function () {
  /* Aquí le asignaremos a la variable una instancia de dataTable; asegura que sea el mismo ID del tag table */
  dataTable = $("#datatable").DataTable({
    processing: true,
    serverSide: true, /* Ojo, este valor debe ser true para habilitar la comunicación con el servidor */
    responsive: true,
    pagingType: "full_numbers",
    ajax: {
      /* Asegúrate de que la ruta (web.php) sea de tipo GET */
      url: baseUrl + "/modulo/datatable",
      dataSrc: "data",
    },
    columns: [
      /* El nombre debe ser el mismo que el del arreglo asociativo devuelto desde el BackEnd */
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
    /* Recuerda que puedes aplicar la configuración de idioma de tu región. */
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json",
    },
  });
});
```

#### HTML
En el HTML deberás contar con una estructura similar a la siguiente. Asegúrate de que coincidan la cantidad de columnas definidas en el JS con las definidas en el HTML:

```html
<!-- Asegúrate de tener la importación de jQuery y la biblioteca DataTable -->

<!-- Deberás crear la estructura de la tabla y asignarle un ID que se utilizará como selector para convertirla en una "DataTable". Además, asegúrate de que los encabezados coincidan con el número de columnas configurado en JavaScript. -->
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

## Creador
- 🇨🇴 Raúl Mauricio Uñate Castro
- Correo electrónico: raulmauriciounate@gmail.com

## Licencia
Este proyecto se encuentra bajo la [Licencia MIT](https://choosealicense.com/licenses/mit/).

🌟 ¡Apoya Mis Proyectos! 🚀

¡Realiza las contribuciones que veas necesarias, el código es totalmente tuyo. Juntos podemos hacer cosas asombrosas y mejorar el mundo del desarrollo. Tu apoyo es invaluable. ✨

Si tienes ideas, sugerencias o simplemente deseas colaborar, ¡estamos abiertos a todo! ¡Únete a nuestra comunidad y forma parte de nuestro viaje hacia el éxito! 🌐👩‍💻👨‍💻
