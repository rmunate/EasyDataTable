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
        return $this->request->columns[$this->request->input('order.0.column')]["data"];
    }

    /**
     * Get the ordering direction from the DataTable.
     *
     * @return string|null
     */
    public function direction()
    {
        return $this->request->input('order.0.dir');
    }

    /**
     * Get the search value from the DataTable input.
     *
     * @return string|null
     */
    public function inputSearch()
    {
        return $this->request->input('search.value');
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
