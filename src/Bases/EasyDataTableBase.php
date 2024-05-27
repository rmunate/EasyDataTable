<?php

namespace Rmunate\EasyDatatable\Bases;

use BadMethodCallException;

/**
 * Base class for EasyDataTable. Provides foundational functionality for
 * handling datatables in a generic and reusable way.
 */
abstract class EasyDataTableBase
{
    /**
     * Handle calls to undefined methods on the instance.
     *
     * This magic method is triggered when invoking inaccessible methods in an object context.
     *
     * @param string $method The name of the method being called.
     * @param array $parameters An enumerated array containing the parameters passed to the method.
     *
     * @throws \BadMethodCallException If the method does not exist.
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        // Throw an exception indicating the called method does not exist
        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.',
            static::class,
            $method
        ));
    }

    /**
     * Initialize a datatable instance.
     *
     * This static method allows for initializing a new instance of the derived class.
     * This can be useful for fluent interfaces and method chaining.
     *
     * @return static A new instance of the called class.
     */
    public static function init()
    {
        // Return a new instance of the called class
        return new static();
    }
}
