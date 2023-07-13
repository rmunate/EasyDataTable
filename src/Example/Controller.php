<?php

class NovedadController extends Controller
{
    //SERVERSIDE
    public function dataTableSS(Request $request)
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
    
        $editar = Auth::user()->can('proyectos.editar');
    
        $datatable = new EasyDataTable();
        $datatable->serverSide();
        $datatable->request($request);
        $datatable->query($query);
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

    //CLIENTSIDE
    ## =====> Data de la tabla
    public function dataTableCS(Request $request)
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

        $editar = Auth::user()->can('proyectos.editar');

        $datatable = new EasyDataTable();
        $datatable->clientSide();
        $datatable->query($query);
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
        return $datatable->response();

    }

}

