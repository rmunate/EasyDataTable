<?php

namespace Rmunate\EasyDatatable\Exceptions;

use BadMethodCallException;

/**
 * Custom exception class for handling datatable-related errors.
 *
 * Extends the BadMethodCallException to provide more specific exception handling
 * for datatable operations.
 */
class DatatableException extends BadMethodCallException
{
    /**
     * Create a new DatatableException instance.
     *
     * This static method provides a convenient way to create a new instance
     * of DatatableException with a custom error message.
     *
     * @param string $message The exception message to be displayed.
     *
     * @return static A new instance of DatatableException.
     */
    public static function create(string $message)
    {
        // Return a new instance of DatatableException with the provided message
        return new static("Rmunate\\EasyDatatable - Exception: {$message}");
    }
}
