<?php

namespace Rmunate\EasyDatatable\Traits;

use Rmunate\PhpConfigRuntime\PhpRunTime;

trait Init
{
    private $maxExecutionTime;
    private $memoryLimit;

    /**
     * Set the maximum execution time for the script.
     *
     * @param int $seconds Maximum execution time in seconds
     *
     * @return $this
     */
    public function maxExecutionTime(int $seconds = 60)
    {
        $this->maxExecutionTime = $seconds;
        ini_set('max_execution_time', $seconds);

        return $this;
    }

    /**
     * Set the memory limit for the script.
     *
     * @param string $limit Memory limit
     *
     * @return $this
     */
    public function memoryLimit(string $limit = '256M')
    {
        $this->memoryLimit = $limit;
        ini_set('memory_limit', $limit);

        return $this;
    }
}
