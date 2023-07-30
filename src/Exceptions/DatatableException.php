<?php

namespace Rmunate\EasyDatatable\Exceptions;

use BadMethodCallException;

class DatatableException extends BadMethodCallException
{
    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @return static
     */
    public static function create(string $message)
    {
        return new static($message);
    }
}
