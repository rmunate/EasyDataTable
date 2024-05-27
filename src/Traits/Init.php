<?php

namespace Rmunate\EasyDatatable\Traits;

/**
 * Trait Init.
 *
 * Provides methods to configure the PHP runtime environment,
 * such as setting the maximum execution time and memory limit.
 */
trait Init
{
    /**
     * @var int|null The maximum execution time for the script in seconds.
     */
    private $maxExecutionTime;

    /**
     * @var string|null The memory limit for the script.
     */
    private $memoryLimit;

    /**
     * Set the maximum execution time for the script.
     *
     * This method sets the `max_execution_time` directive in PHP to the specified
     * number of seconds, allowing long-running scripts to complete without timing out.
     *
     * @param int $seconds Maximum execution time in seconds. Default is 60 seconds.
     *
     * @return $this
     */
    public function maxExecutionTime(int $seconds = 60)
    {
        // Store the maximum execution time in the instance variable
        $this->maxExecutionTime = $seconds;

        // Set the PHP configuration for maximum execution time
        ini_set('max_execution_time', $seconds);

        // Allow method chaining by returning the current instance
        return $this;
    }

    /**
     * Set the memory limit for the script.
     *
     * This method sets the `memory_limit` directive in PHP to the specified
     * value, allowing scripts to use more memory if necessary.
     *
     * @param string $limit Memory limit. Default is '256M'.
     *
     * @return $this
     */
    public function memoryLimit(string $limit = '256M')
    {
        // Store the memory limit in the instance variable
        $this->memoryLimit = $limit;

        // Set the PHP configuration for memory limit
        ini_set('memory_limit', $limit);

        // Allow method chaining by returning the current instance
        return $this;
    }
}
