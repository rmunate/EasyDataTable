<?php

namespace Rmunate\EasyDatatable\Contracts;

use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

/** Fluent configuration and response contract for a DataTables endpoint. */
interface IEasyDataTable
{
    /**
     * Select database-backed filtering, ordering and pagination.
     *
     * @return $this
     */
    public function serverSide();

    /**
     * Select client-side processing and return all rows in one response.
     *
     * @return $this
     */
    public function clientSide();

    /**
     * Set the DataTables request used for server-side processing.
     *
     * @param Request $request
     *
     * @return $this
     */
    public function request(Request $request);

    /**
     * Use an unexecuted query as the row source, replacing any prepared rows.
     *
     * @param Builder $query
     *
     * @return $this
     */
    public function query(Builder $query);

    /**
     * Replace the query with prepared rows and select client-side mode.
     *
     * @param iterable $data A sequence of rows, not a single associative row.
     * @return $this
     */
    public function fromData(iterable $data);

    /**
     * Transform each returned row after fetching or pagination.
     *
     * @param Closure(object): mixed $map
     *
     * @return $this
     */
    public function map(Closure $map);

    /**
     * Override global server-side search with a callback returning the filtered query.
     *
     * @param Closure(Builder, string): Builder $search
     *
     * @return $this
     */
    public function search(Closure $search);

    /**
     * Generate the response for the datatable.
     *
     * @return array{data: list<mixed>, draw?: int, recordsTotal?: int, recordsFiltered?: int}
     * @throws \Rmunate\EasyDatatable\Exceptions\DatatableException For invalid configuration or request fields.
     */
    public function response();
}
