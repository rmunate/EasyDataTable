<?php

namespace Rmunate\EasyDatatable\Traits;

/** Configure PHP runtime limits for the current script. */
trait Init
{
    /**
     * Set PHP's maximum execution time in seconds.
     *
     * @return $this
     */
    public function maxExecutionTime(int $seconds = 60)
    {
        ini_set('max_execution_time', (string) $seconds);

        return $this;
    }

    /**
     * Set PHP's memory limit using an ini quantity such as '512M'.
     *
     * @return $this
     */
    public function memoryLimit(string $limit = '512M')
    {
        ini_set('memory_limit', $limit);

        return $this;
    }
}
