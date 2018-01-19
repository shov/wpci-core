<?php declare(strict_types=1);

namespace Wpci\Core\Flow;

use Wpci\Core\Contracts\PromiseManagerInterface;
use Wpci\Core\Wordpress\WpProvider;

/**
 * The instance is the proxy to wordpress hook (actions) system
 */
class PromiseManager implements PromiseManagerInterface
{
    /** @var WpProvider */
    protected $wpProvider;

    /**
     * DI
     * @param WpProvider $wpProvider
     */
    public function __construct(WpProvider $wpProvider)
    {
        $this->wpProvider = $wpProvider;
    }

    /**
     * {@inheritdoc}
     * @throws \ErrorException
     */
    public function addPromise(string $name, callable $promise, ?int $priority = null)
    {
        $this->wpProvider
            ->addAction($name, $promise, $priority);
    }

    /**
     * {@inheritdoc}
     * @param array $params
     * @throws \ErrorException
     */
    public function callPromise(string $name, ...$params)
    {
        $this->wpProvider
            ->doAction($name, ...$params);
    }
}