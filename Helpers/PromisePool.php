<?php declare(strict_types=1);

namespace Wpci\Core\Helpers;

/**
 * The pool of callbacks
 */
class PromisePool
{
    /**
     * @var array
     */
    protected $pool = [];

    /**
     * Add callback to the pool
     * @param callable $promise
     * @param int|null $priority
     */
    public function addPromise(callable $promise, ?int $priority = null)
    {
        $priority = $priority ?? 0;

        $pool[] = compact('priority', 'promise');
    }

    /**
     * Call all callbacks ordered by priority
     */
    public function callAllPromises()
    {
        $pool = $this->pool;

        usort($pool, function ($a, $b) {
            return $a['priority'] - $b['priority'];
        });

        foreach ($pool as $promise) {
            $promise();
        }
    }
}