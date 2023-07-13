<?php

namespace Rmunate\EasyDatatable\Bases;

use BadMethodCallException;
use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

abstract class EasyDataTableBase
{
    /**
     * Handle calls to missing methods on the helper.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.',
            static::class,
            $method
        ));
    }

    /**
     * Initialize a datatable of any type.
     *
     * @return static
     */
    public static function init()
    {
        return new static();
    }

    /**
     * Determine if the datatable is server-side.
     *
     * @return mixed
     */
    abstract public function serverSide();

    /**
     * Determine if the datatable is not server-side.
     *
     * @return mixed
     */
    abstract public function clientSide();

    /**
     * Get the request instance for the datatable.
     *
     * @return mixed
     */
    abstract public function request(Request $request);

    /**
     * Get the query instance for the datatable.
     *
     * @return mixed
     */
    abstract public function query(Builder $query);

    /**
     * Get the map closure for the datatable.
     *
     * @return mixed
     */
    abstract public function map(Closure $map);

    /**
     * Get the search closure for the datatable.
     *
     * @return mixed
     */
    abstract public function search(Closure $search);

    /**
     * Generate the response for the datatable.
     *
     * @return mixed
     */
    abstract public function response();
}
