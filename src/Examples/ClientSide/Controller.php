<?php

use App\Http\Controllers\Controller;
use Rmunate\EasyDatatable\EasyDataTable;

class Modulo extends Controller
{
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
            ->where('empleados.empresa', $request->company); /* (Opcional) */

        /* (Opcional) */
        $permissionEdit = Auth::user()->can('novedades.editar');

        $datatable = new EasyDataTable();
        $datatable->clientSide();
        $datatable->query($query);
        $datatable->map(function ($row) use ($permissionEdit) { /* (Opcional)*/
            return [
                'identification' => $row->identification, /*  */
                'employee' => strtolower($row->employee),
                'novelty_type' => strtolower($row->novelty_type),
                'description' => strtolower($row->description),
                'calendar_days' => $row->calendar_days,
                'business_days' => $row->business_days,
                'initial_date' => date('d/m/Y', strtotime($row->initial_date)),
                'final_date' => date('d/m/Y', strtotime($row->final_date)),
                "action" => [
                    "editar" => $permissionEdit,
                ],
            ];
        });
        return $datatable->response();
    }
}
