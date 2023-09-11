<?php

namespace Rmunate\EasyDatatable\Traits;

trait Client
{
    /**
     * Get the limit value sent by the DataTable.
     *
     * @return int|null
     */
    public function limit()
    {
        return $this->request->input('length');
    }

    /**
     * Get the start value for the database query based on DataTable input.
     *
     * @return int|null
     */
    public function start()
    {
        return $this->request->input('start');
    }

    /**
     * Get the requested ordering column from the DataTable.
     *
     * @return string|null
     */
    public function order()
    {
        $column = ($this->request->has('order.0.column')) ? $this->request->input('order.0.column') : $this->request->input('order.column');

        return $this->request->columns[$column]['data'];
    }

    /**
     * Get the ordering direction from the DataTable.
     *
     * @return string|null
     */
    public function direction()
    {
        $order = $this->request->has('order.0.dir') ? $this->request->input('order.0.dir') : $this->request->input('order.dir');

        return $order;
    }

    /**
     * Get the search value from the DataTable input.
     *
     * @return string|null
     */
    public function inputSearch()
    {
        $search = $this->request->has('search.value') ? $this->request->input('search.value') : $this->request->input('search');

        return $search;
    }

    /**
     * Get the "draw" value sent by the DataTable.
     *
     * @return int|null
     */
    public function draw()
    {
        return intval($this->request->input('draw'));
    }
}
