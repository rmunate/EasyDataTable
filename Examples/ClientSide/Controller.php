<?php

use Rmunate\EasyDatatable\EasyDataTable;

class NameController extends Controller
{
    /**
     * ES: Define un método que maneje la ruta tipo GET recibiendo una variable de tipo Request.
     * EN: Define a method that handles the GET type route receiving a Request type variable.
     */
    public function dataTable(Request $request)
    {
        /**
         * ES: Crea una consulta con Query Builder, puedes aplicar todas las condiciones que requieras, solo NO apliques el método final get().
         * EN Create a query using Query Builder, you can apply all the conditions you require, just DO NOT apply the final get() method.
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
            )
            ->where('empleados.empresa', $request->company);

        /**
         * ES: (Opcional) A veces, para determinar si un usuario tiene permisos sobre alguna acción en las filas de la tabla, debes realizar consultas como estas.
         * EN: (Optional) Sometimes, to determine if a user has permissions for some action on the table rows, you need to make queries like these.
         */
        $permissionEdit = Auth::user()->can('novedades.editar');

        /**
         * ES: El uso de la librería es tan simple como emplear el siguiente código.
         * EN: Using the library is as simple as using the following code.
         */
        $datatable = new EasyDataTable();
        $datatable->clientSide(); /* Obligatorio / Requerid */
        $datatable->query($query);
        $datatable->map(function ($row) use ($permissionEdit) {
            /**
             * ES: (Opcional) Si necesitas personalizar la forma en que se visualiza la información en la tabla, el método map() será de gran ayuda.
             * Además, si necesitas enviar datos adicionales o realizar validaciones, aquí puedes aplicar la lógica.
             * Es importante que el alias de cada valor en la consulta sea el mismo valor que se utiliza en el array, como se muestra a continuación.
             */

            /**
             * EN: (Optional) If you need to customize how the information is displayed in the table, the map() method will be very helpful.
             * Additionally, if you need to send additional data or perform validations, you can apply the logic here.
             * It's important that the alias of each value in the query is the same as the value used in the array, as shown below.
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
         * ES: Este método retorna la estructura construida tal como la requiere la librería DataTable en el Front.
         * EN: This method returns the constructed structure as required by the DataTable library on the Front.
         */
        return $datatable->response();
    }
}
