<?php

namespace Rmunate\EasyDatatable;

use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Rmunate\EasyDatatable\Bases\EasyDataTableBase;
use Rmunate\EasyDatatable\Exceptions\Exception;
use Rmunate\EasyDatatable\Traits\Client;
use Rmunate\EasyDatatable\Traits\Init;

class EasyDataTable extends EasyDataTableBase
{
    use Init;
    use Client;

    /* Server-side properties */
    private $request;
    private $query;
    private $map;
    private $search;
    private $serverSide = false;
    private $clientSide = true;

    /**
     * Create a new EasyDataTable instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->maxExecutionTime();
        $this->memoryLimit();
    }

    /**
     * Set the server-side mode for the EasyDataTable.
     *
     * @return $this
     */
    public function serverSide()
    {
        $this->serverSide = true;

        return $this;
    }

    /**
     * Set the client-side mode for the EasyDataTable.
     *
     * @return $this
     */
    public function clientSide()
    {
        $this->serverSide = false;

        return $this;
    }

    /**
     * Set the request for the EasyDataTable.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return $this
     */
    public function request(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set the query for the EasyDataTable.
     *
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return $this
     */
    public function query(Builder $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Set the map closure for the EasyDataTable.
     *
     * @param \Closure $map
     *
     * @return $this
     */
    public function map(Closure $map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * Set the search closure for the EasyDataTable.
     *
     * @param \Closure $search
     *
     * @return $this
     */
    public function search(Closure $search)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Get the response for the EasyDataTable.
     *
     * @return array
     */
    public function response()
    {
        if ($this->serverSide) {
            return $this->dataServerSide();
        } else {
            if (!empty($this->search)) {
                Exception::disabledSearch();
            }

            return $this->dataClientSide();
        }
    }

    /**
     * Get the response for client-side EasyDataTable.
     *
     * @return array
     */
    private function dataClientSide()
    {
        $rows = $this->query->get();

        $data = [];
        foreach ($rows as $r) {
            $item = !empty($this->map) ? ($this->map)($r) : $r;
            $data[] = $item;
        }

        return [
            'data' => $data,
        ];
    }

    /**
     * Get the response for server-side EasyDataTable.
     *
     * @return array
     */
    private function dataServerSide()
    {
        $limit = $this->limit();
        $start = $this->start();
        $order = $this->order();
        $dir = $this->direction();

        if (empty($this->inputSearch())) {
            $rows = $this->query->offset($start)->limit($limit)->orderBy($order, $dir)->get();
            $totalFiltered = $this->query->count();
        } else {
            $search = $this->inputSearch();

            if (!empty($this->search)) {
                $newRows = ($this->search)($this->query, $search);
            } else {
                $columns = $this->query->columns;
                $tableAndField = [];

                foreach ($columns as $column) {
                    if (strpos($column, '*') === false) {
                        $tableAndField[] = explode(' ', $column)[0];
                    }
                }

                $newRows = $this->query->where(function ($query) use ($search, $tableAndField) {
                    foreach ($tableAndField as $field) {
                        $query->orWhere($field, 'LIKE', "%{$search}%");
                    }
                });
            }

            $totalFiltered = $newRows->count();
            $rows = $newRows->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        }

        $data = [];
        foreach ($rows as $r) {
            $item = !empty($this->map) ? ($this->map)($r) : $r;
            $data[] = $item;
        }

        return [
            'draw' => $this->draw(),
            'recordsTotal' => $this->query->count(),
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
    }
}
