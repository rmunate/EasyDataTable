<?php

namespace Rmunate\EasyDatatable\Contracts;

use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

interface IEasyDataTable
{
    /**
     * Determine if the datatable is server-side.
     *
     * @return mixed
     */
    public function serverSide();

    /**
     * Determine if the datatable is not server-side.
     *
     * @return mixed
     */
    public function clientSide();

    /**
     * Get the request instance for the datatable.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function request(Request $request);

    /**
     * Get the query instance for the datatable.
     *
     * @param Builder $query
     *
     * @return mixed
     */
    public function query(Builder $query);

    /**
     * Get the map closure for the datatable.
     *
     * @param Closure $map
     *
     * @return mixed
     */
    public function map(Closure $map);

    /**
     * Get the search closure for the datatable.
     *
     * @param Closure $search
     *
     * @return mixed
     */
    public function search(Closure $search);

    /**
     * Generate the response for the datatable.
     *
     * @return mixed
     */
    public function response();
}
