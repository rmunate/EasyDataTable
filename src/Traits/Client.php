<?php

namespace Rmunate\EasyDatatable\Traits;

use Rmunate\EasyDatatable\Exceptions\DatatableException;

/**
 * Trait Client.
 *
 * Provides helper methods for interacting with DataTables requests.
 */
trait Client
{
    /**
     * Get the limit value sent by the DataTable.
     *
     * @throws \Rmunate\EasyDatatable\Exceptions\DatatableException If 'length' is not found in the request.
     *
     * @return int|null
     */
    public function limit()
    {
        if ($this->request->has('length')) {
            return $this->request->input('length');
        }

        throw DatatableException::create("Property 'length' not found in the request.");
    }

    /**
     * Get the start value for the database query based on DataTable input.
     *
     * @throws \Rmunate\EasyDatatable\Exceptions\DatatableException If 'start' is not found in the request.
     *
     * @return int|null
     */
    public function start()
    {
        if ($this->request->has('start')) {
            return $this->request->input('start');
        }

        throw DatatableException::create("Property 'start' not found in the request.");
    }

    /**
     * Get the requested ordering column from the DataTable.
     *
     * @throws \Rmunate\EasyDatatable\Exceptions\DatatableException If 'order.0.column' or 'order.column' is not found in the request.
     *
     * @return string|null
     */
    public function order()
    {
        if ($this->request->has('order.0.column')) {
            $column = $this->request->input('order.0.column');
        } elseif ($this->request->has('order.column')) {
            $column = $this->request->input('order.column');
        } else {
            // throw DatatableException::create("Property 'order.0.column' or 'order.column' not found in the request.");
            $column = 0;
        }

        return $this->request->columns[$column]['data'];
    }

    /**
     * Get the ordering direction from the DataTable.
     *
     * @throws \Rmunate\EasyDatatable\Exceptions\DatatableException If 'order.0.dir' or 'order.dir' is not found in the request.
     *
     * @return string|null
     */
    public function direction()
    {
        if ($this->request->has('order.0.dir')) {
            return $this->request->input('order.0.dir');
        } elseif ($this->request->has('order.dir')) {
            return $this->request->input('order.dir');
        }

        return 'asc';
        // throw DatatableException::create("Property 'order.0.dir' or 'order.dir' not found in the request.");
    }

    /**
     * Get the search value from the DataTable input.
     *
     * @throws \Rmunate\EasyDatatable\Exceptions\DatatableException If 'search.value' or 'search' is not found in the request.
     *
     * @return string|null
     */
    public function inputSearch()
    {
        if ($this->request->has('search.value')) {
            return $this->request->input('search.value');
        } elseif ($this->request->has('search')) {
            return $this->request->input('search');
        }

        throw DatatableException::create("Property 'search' not found in the request.");
    }

    /**
     * Get the "draw" value sent by the DataTable.
     *
     * @throws \Rmunate\EasyDatatable\Exceptions\DatatableException If 'draw' is not found in the request.
     *
     * @return int|null
     */
    public function draw()
    {
        if ($this->request->has('draw')) {
            return intval($this->request->input('draw'));
        }

        throw DatatableException::create("Property 'draw' not found in the request.");
    }
}
