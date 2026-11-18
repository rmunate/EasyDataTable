<?php

namespace Rmunate\EasyDatatable\Traits;

use Rmunate\EasyDatatable\Exceptions\DatatableException;

/** Read and validate DataTables parameters from the configured Laravel request. */
trait Client
{
    /**
     * Accept integers and integer form values at or above the field's minimum.
     *
     * @param mixed $value Raw request value.
     *
     * @throws DatatableException If the value is missing, malformed or out of range.
     */
    private function integerInput($value, string $key, int $min): int
    {
        if ((!is_string($value) && !is_int($value))
            || filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min]]) === false) {
            throw DatatableException::create("Invalid or missing integer '$key'.");
        }

        return (int) $value;
    }

    /** Return the requested page size, or -1 for all remaining rows. */
    public function limit(): int
    {
        return $this->integerInput($this->input('length'), 'length', -1);
    }

    /** Return the zero-based offset into the filtered result set. */
    public function start(): int
    {
        return $this->integerInput($this->input('start'), 'start', 0);
    }

    /** Return the nonnegative draw counter echoed in the server response. */
    public function draw(): int
    {
        return $this->integerInput($this->input('draw'), 'draw', 0);
    }

    /**
     * Resolve the first order instruction to its client-side data field.
     *
     * @throws DatatableException If the column index or field is invalid.
     *
     * @return string|null Null when the request does not specify an order column.
     */
    public function order(): ?string
    {
        $column = $this->input('order.0.column', $this->input('order.column'));
        if ($column === null) {
            return null;
        }
        $index = $this->integerInput($column, 'order.column', 0);
        $field = $this->input("columns.$index.data");
        if (!is_string($field) || $field === '') {
            throw DatatableException::create('Missing data field for the requested order column.');
        }

        return $field;
    }

    /**
     * Return the requested sort direction, defaulting to ascending.
     *
     * @throws DatatableException For directions other than ascending or descending.
     *
     * @return 'asc'|'desc'
     */
    public function direction(): string
    {
        $dir = $this->input('order.0.dir', $this->input('order.dir', 'asc'));
        if (!is_string($dir) || !in_array(strtolower($dir), ['asc', 'desc'], true)) {
            throw DatatableException::create("Order direction must be 'asc' or 'desc'.");
        }

        return strtolower($dir);
    }

    /** Read nested or flat global search input; the empty string means no filter. */
    public function inputSearch(): string
    {
        $search = $this->input('search.value', $this->input('search'));
        if ($search !== null && !is_scalar($search)) {
            throw DatatableException::create('Invalid search value.');
        }

        return (string) $search;
    }

    /**
     * Read a dot-separated field from the configured request.
     *
     * @param mixed $default Value used when the field is absent.
     *
     * @throws DatatableException If no request was configured.
     *
     * @return mixed
     */
    private function input(string $key, $default = null)
    {
        if (!$this->request) {
            throw DatatableException::create('Call request() before reading DataTables input.');
        }

        return $this->request->input($key, $default);
    }
}
