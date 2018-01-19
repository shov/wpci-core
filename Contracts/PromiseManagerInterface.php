<?php declare(strict_types=1);

namespace Wpci\Core\Contracts;

/**
 * Common promise/hooks interface
 */
interface PromiseManagerInterface
{
    /**
     * Add promise/hook by name
     * @param string $name
     * @param callable $promise
     * @param int|null $priority
     * @return mixed
     */
    public function addPromise(string $name, callable $promise, ?int $priority = null);

    /**
     * Call the promise/hook by name
     * @param string $name
     * @return mixed
     */
    public function callPromise(string $name);
}