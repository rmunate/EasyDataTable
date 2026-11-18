<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Rmunate\EasyDatatable\EasyDataTable;
use Rmunate\EasyDatatable\Exceptions\DatatableException;

/** Exercise both processing modes against an isolated SQLite database. */
final class EasyDataTableTest extends TestCase
{
    /** @var \Illuminate\Database\Query\Builder Base query for the three fixture rows. */
    private $query;

    /** Seed two teams with names that distinguish a literal zero from an empty search. */
    protected function setUp(): void
    {
        $db = new Manager();
        $db->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $connection = $db->getConnection();
        $connection->statement('CREATE TABLE people (id INTEGER, name TEXT, team TEXT)');
        $this->query = $connection->table('people');
        $this->query->insert([
            ['id' => 1, 'name' => 'A0', 'team' => 'a'],
            ['id' => 2, 'name' => 'B', 'team' => 'a'],
            ['id' => 3, 'name' => 'C0', 'team' => 'b'],
        ]);
    }

    /** Create a server-side table with paging defaults overridden by the test request. */
    private function server(array $input = []): EasyDataTable
    {
        $request = Request::create('/', 'GET', $input + ['draw' => '2', 'start' => '0', 'length' => '10']);

        return EasyDataTable::init()->serverSide()->query($this->query)->request($request);
    }

    /** Repeated draws must leave the caller's sort order and row set intact. */
    public function testServerSearchOrderPaginationAndMappingDoNotMutateSource(): void
    {
        $this->query->select('people.name AS label')->orderBy('id');
        $table = $this->server([
            'length' => 1,
            'search' => ['value' => '0'],
            'order' => [['column' => '0', 'dir' => 'desc']],
            'columns' => [['data' => 'label']],
        ])->map(fn ($row) => $row->label);
        $expected = ['draw' => 2, 'recordsTotal' => 3, 'recordsFiltered' => 2, 'data' => ['C0']];
        self::assertSame($expected, $table->response());
        self::assertSame($expected, $table->response());
        self::assertSame(['A0', 'B', 'C0'], $this->query->get()->pluck('label')->all());
    }

    /** Flat ordering and length -1 must respect the offset into filtered rows. */
    public function testDefaultProjectionAndFlatRequestWithAllRemainingRows(): void
    {
        $table = $this->server([
            'length' => '-1', 'start' => '1', 'search' => '0',
            'order' => ['column' => 0, 'dir' => 'asc'],
            'columns' => [['data' => 'id']],
        ]);
        self::assertSame(['C0'], array_column($table->response()['data'], 'name'));
    }

    /** Counts describe projected groups or distinct rows, not their underlying records. */
    public function testGroupedResultsWithCustomSearchAndDistinctCounts(): void
    {
        $this->query->select('team')->selectRaw('count(*) as members')->groupBy('team');
        $result = $this->server(['search' => '2'])
            ->search(fn ($query, $term) => $query->havingRaw('count(*) = CAST(? AS INTEGER)', [$term]))
            ->response();
        self::assertSame(2, $result['recordsTotal']);
        self::assertSame(1, $result['recordsFiltered']);
        self::assertSame(['a'], array_column($result['data'], 'team'));
        $this->query = $this->query->newQuery()->from('people')->select('team')->distinct();
        self::assertSame(2, $this->server()->response()['recordsTotal']);
    }

    /** An offset beyond the final row affects only the data array. */
    public function testEmptyPageDoesNotChangeTotals(): void
    {
        self::assertSame(['draw' => 2, 'recordsTotal' => 3, 'recordsFiltered' => 3, 'data' => []],
            $this->server(['start' => '10'])->response());
    }

    /** A prepared iterable, including an empty one, replaces the client query. */
    public function testClientRowsFromQueryAndPreparedIterable(): void
    {
        $table = EasyDataTable::init()->clientSide()->query($this->query->orderBy('id'));
        self::assertSame(['A0', 'B', 'C0'], array_column($table->response()['data'], 'name'));
        $rows = new ArrayIterator([7 => ['name' => 'Ada']]);
        self::assertSame(['data' => ['Ada']], $table->fromData($rows)->map(fn ($row) => $row->name)->response());
        self::assertSame(['data' => []], $table->fromData([])->response());
    }

    /** A real database column is not orderable unless exposed in the projection. */
    public function testOrderCannotReferenceAnUnselectedColumn(): void
    {
        $this->query->select('id');
        $this->expectException(DatatableException::class);
        $this->server(['columns' => [['data' => 'team']], 'order' => [['column' => 0]]])->response();
    }

    /** Negative page sizes other than the all-rows sentinel are invalid. */
    public function testInvalidPaginationIsReported(): void
    {
        $this->expectException(DatatableException::class);
        $this->server(['length' => -2])->response();
    }

    /** A table carrying a server request must not silently switch to client processing. */
    public function testClientRejectsServerOptions(): void
    {
        $this->expectException(DatatableException::class);
        $this->server()->clientSide()->response();
    }

    /** A query alone is insufficient to process a server-side draw. */
    public function testIncompleteServerConfigurationIsReported(): void
    {
        $this->expectException(DatatableException::class);
        EasyDataTable::init()->serverSide()->query($this->query)->response();
    }
}
