<?php

namespace Rmunate\EasyDatatable;

use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Rmunate\EasyDatatable\Bases\EasyDataTableBase;
use Rmunate\EasyDatatable\Contracts\IEasyDataTable;
use Rmunate\EasyDatatable\Exceptions\DatatableException;
use Rmunate\EasyDatatable\Traits\Client;
use Rmunate\EasyDatatable\Traits\Init;

/** Builds DataTables responses from a Laravel query or prepared rows. */
class EasyDataTable extends EasyDataTableBase implements IEasyDataTable
{
    use Client;
    use Init;

    /** @var Request|null Incoming draw, search, ordering and paging parameters. */
    private ?Request $request = null;
    /** @var Builder|null Caller-owned query; server-side processing uses a clone. */
    private ?Builder $query = null;
    /** @var Closure|null Optional transformation applied to returned rows. */
    private ?Closure $map = null;
    /** @var Closure|null Optional replacement for the default global search. */
    private ?Closure $search = null;
    /** @var bool|null Null until a processing mode is selected. */
    private ?bool $serverSide = null;
    /** @var iterable|null Prepared client-side rows; an empty iterable is valid. */
    private ?iterable $data = null;

    /** Apply the package defaults for execution time and memory. */
    public function __construct()
    {
        $this->maxExecutionTime();
        $this->memoryLimit();
    }

    /** {@inheritDoc} */
    public function serverSide()
    {
        $this->serverSide = true;

        return $this;
    }

    /** {@inheritDoc} */
    public function clientSide()
    {
        $this->serverSide = false;

        return $this;
    }

    /** {@inheritDoc} */
    public function request(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /** {@inheritDoc} */
    public function query(Builder $query)
    {
        $this->query = $query;
        $this->data = null;

        return $this;
    }

    /** {@inheritDoc} */
    public function fromData(iterable $data)
    {
        $this->data = $data;
        $this->query = null;

        return $this->clientSide();
    }

    /** {@inheritDoc} */
    public function map(Closure $map)
    {
        $this->map = $map;

        return $this;
    }

    /** {@inheritDoc} */
    public function search(Closure $search)
    {
        $this->search = $search;

        return $this;
    }

    /** {@inheritDoc} */
    public function response(): array
    {
        if ($this->serverSide === null) {
            throw DatatableException::create('Select clientSide() or serverSide() before calling response().');
        }

        if ($this->serverSide) {
            if (!$this->query || !$this->request) {
                throw DatatableException::create('Server-side mode requires query() and request().');
            }

            return $this->dataServerSide();
        }

        if ($this->request || $this->search) {
            throw DatatableException::create('request() and search() are only available in server-side mode.');
        }
        if ($this->data === null && !$this->query) {
            throw DatatableException::create('Client-side mode requires query() or fromData().');
        }

        return ['data' => $this->rows($this->data ?? $this->query->get())];
    }

    /**
     * Reindex rows for JSON serialization, applying the optional mapper.
     *
     * @return list<mixed>
     */
    private function rows(iterable $rows): array
    {
        $data = [];
        foreach ($rows as $row) {
            $data[] = $this->map ? ($this->map)((object) $row) : $row;
        }

        return $data;
    }

    /**
     * Count before and after filtering, then fetch only the requested page.
     *
     * @return array{draw: int, recordsTotal: int, recordsFiltered: int, data: list<mixed>}
     */
    private function dataServerSide(): array
    {
        $query = clone $this->query;
        $draw = $this->draw();
        $start = $this->start();
        $length = $this->limit();
        $order = $this->order();
        $direction = $this->direction();
        $search = $this->inputSearch();
        $fields = $order !== null || ($search !== '' && !$this->search) ? $this->selectedFields($query) : [];
        if ($order !== null && !isset($fields[$order])) {
            throw DatatableException::create('The order column must be a selected column or alias.');
        }

        $total = $filtered = $this->countRows($query);
        if ($search !== '') {
            if ($this->search) {
                $query = ($this->search)($query, $search);
                if (!$query instanceof Builder) {
                    throw DatatableException::create('The search callback must return a query builder.');
                }
            } else {
                if (!$fields) {
                    throw DatatableException::create('Select searchable columns or provide a search callback.');
                }
                $query->where(function ($nested) use ($fields, $search) {
                    foreach ($fields as $field) {
                        $nested->orWhere($field, 'like', "%{$search}%");
                    }
                });
            }
            $filtered = $this->countRows($query);
        }

        if ($order !== null) {
            $query->reorder($order, $direction);
        }
        $rows = $query->offset($start)->limit($length === -1 ? PHP_INT_MAX : $length)->get();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $this->rows($rows),
        ];
    }

    /** Count result rows, wrapping grouped, distinct or union queries to retain their semantics. */
    private function countRows(Builder $query): int
    {
        if (!$query->distinct && !$query->groups && !$query->havings && !$query->unions) {
            return $query->count();
        }

        return $query->newQuery()->fromSub((clone $query)->reorder(), 'datatable_rows')->count();
    }

    /**
     * Map projected field names and aliases to their underlying searchable columns.
     *
     * @return array<string, string>
     */
    private function selectedFields(Builder $query): array
    {
        $fields = [];
        foreach ($query->columns ?? ['*'] as $column) {
            if ($column === '*' && is_string($query->from) && !$query->joins && preg_match('/^\w+$/D', $query->from)) {
                foreach ($query->getConnection()->getSchemaBuilder()->getColumnListing($query->from) as $name) {
                    $fields[$name] = $name;
                }
            } elseif (is_string($column) && preg_match('/^((?:\w+\.)*(\w+))(?:\s+as\s+(\w+))?$/iD', $column, $parts)) {
                $fields[$parts[3] ?? $parts[2]] = $parts[1];
            }
        }

        return $fields;
    }
}
