<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rmunate\EasyDatatable\EasyDataTable;

/** Present employee leave requests from the signed-in user's company. */
class LeaveTableController
{
    /** Return the full, small result set for a browser-managed table. */
    public function client(Request $request): array
    {
        $query = $this->visibleRequests($request);
        $canReview = $request->user()->can('review-leave-requests');

        return EasyDataTable::init()
            ->clientSide()
            ->query($query)
            ->map(fn ($row) => $this->present($row, $canReview))
            ->response();
    }

    // #region server-action
    /** Apply dashboard filters and process a single DataTables draw. */
    public function server(Request $request): array
    {
        $query = $this->visibleRequests($request);
        $filters = $request->validate([
            'length'        => ['required', 'integer', 'between:1,100'],
            'status'        => ['nullable', 'in:pending,approved,rejected'],
            'started_since' => ['nullable', 'date_format:Y-m-d'],
        ]);
        if (isset($filters['status'])) {
            $query->where('leave_requests.status', $filters['status']);
        }
        if (isset($filters['started_since'])) {
            $query->where('leave_requests.starts_on', '>=', $filters['started_since']);
        }

        $canReview = $request->user()->can('review-leave-requests');

        return EasyDataTable::init()
            ->serverSide()
            ->request($request)
            ->query($query)
            ->search(static function (Builder $query, string $term): Builder {
                return $query->where(static function (Builder $match) use ($term): void {
                    $match->where('employees.name', 'like', "%{$term}%")
                        ->orWhere('employees.code', 'like', "%{$term}%")
                        ->orWhere('leave_types.name', 'like', "%{$term}%")
                        ->orWhere('leave_requests.reason', 'like', "%{$term}%");
                });
            })
            ->map(fn ($row) => $this->present($row, $canReview))
            ->response();
    }
    // #endregion server-action

    /** Show a pending request to a reviewer; this GET endpoint does not update it. */
    public function review(Request $request, int $id): array
    {
        $query = $this->visibleRequests($request);
        abort_unless($request->user()->can('review-leave-requests'), 403);

        $row = $query->where('leave_requests.id', $id)->where('leave_requests.status', 'pending')->first();
        abort_if($row === null, 404);

        return $this->present($row, false);
    }

    // #region scoped-query
    /** Keep tenant restrictions and the public column list common to both modes. */
    private function visibleRequests(Request $request): Builder
    {
        $companyId = $request->user()->company_id ?? null;
        abort_if($companyId === null, 403);

        return DB::table('leave_requests')
            ->join('employees', 'employees.id', '=', 'leave_requests.employee_id')
            ->leftJoin('leave_types', 'leave_types.id', '=', 'leave_requests.leave_type_id')
            ->where('employees.company_id', $companyId)
            ->select(
                'leave_requests.id as request_id',
                'employees.code as employee_code',
                'employees.name as employee',
                'leave_types.name as leave_type',
                'leave_requests.reason',
                'leave_requests.starts_on',
                'leave_requests.status'
            )
            ->orderByDesc('leave_requests.id');
    }
    // #endregion scoped-query

    // #region row-map
    /** Preserve ISO dates for sorting and expose a review link only for eligible rows. */
    private function present(object $row, bool $canReview): array
    {
        return [
            'request_id'    => (int) $row->request_id,
            'employee_code' => $row->employee_code,
            'employee'      => $row->employee,
            'leave_type'    => $row->leave_type ?? 'Uncategorized',
            'reason'        => $row->reason,
            'starts_on'     => $row->starts_on,
            'status'        => $row->status,
            'review_url'    => $canReview && $row->status === 'pending'
                ? route('examples.leave.review', ['id' => $row->request_id], false)
                : null,
        ];
    }
    // #endregion row-map
}
