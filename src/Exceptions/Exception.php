<?php

namespace Rmunate\EasyDatatable\Exceptions;

use BadMethodCallException;

class Exception
{
    /**
     * Exception use method search in clientside.
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public static function disabledSearch()
    {
        throw new BadMethodCallException("The '->search()' method is only available for DataTables ServerSide '->serverSide()'");
    }
}
