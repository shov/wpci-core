<?php declare(strict_types=1);

namespace Wpci\Core;

use Monolog\Logger;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Wpci\Core\Facades\ShutdownPromisePool;
use Wpci\Core\Helpers\Path;
use Wpci\Core\Helpers\PromisePool;
use Wpci\Core\Helpers\ServiceRegistrator;
use Wpci\Core\Http\WpFrontController;
use wpdb;

/**
 * Class App
 * @package App
 */
final class App
{
    use ServiceRegistrator;

    /** @var ContainerBuilder */
    protected $container;

    /** @var Path */
    protected $path;

    /** @var Logger */
    protected $logger = null;

    /** @var array */
    protected $env = [];

    /**
     * App constructor. Bootstrap
     * @param string $rootPath
     * @throws \Exception
     */
    public function __construct(string $rootPath)
    {
        $this->container = new ContainerBuilder();

        $this->path = new Path($rootPath);

        ini_set('error_log', $this->path->getProjectRoot('/error.log.wp.txt'));

        /**
         * Wordpress
         * TODO: remove dependency
         */
        global $wpdb;
        global $wp_query;
        global $wp;

        $this->container->set(wpdb::class, $wpdb);
        $this->container->set("wp", $wp);
        $this->container->set("wp.query", $wp_query);

        add_action('wp', function () {
            global $post;
            $this->container->set("wp.post", $post);
        });

        /**
         * Core
         */
        $this->prepareArguments('Wpci\Core\WpFrontController', new Reference('service_container'));
        $this->prepareArguments('Wpci\Core\Render\View',
            new Reference('Wpci\Core\Render\MustacheTemplate'),
            new Reference('Wpci\Core\Http\WpResponse')
        );

        $this->walkDirForServices('DataSource');
        $this->walkDirForServices('Helpers');
        $this->walkDirForServices('Http');
        $this->walkDirForServices('Render');

        /**
         * App
         */
        $serviceConfigLoader = new YamlFileLoader($this->container, new FileLocator($this->path->getConfigPath()));
        $serviceConfigLoader->load('services.yaml');

        $this->container->set('promise-pool.shutdown', new PromisePool());

        /**
         * Environment
         * TODO: move to config
         */
        $this->env['testing'] = true;
    }

    /**
     * Run the App
     */
    public function run()
    {
        try {
            $this->container->compile();

            /**
             * TODO: remove dependency
             */
            add_action('shutdown', function () {
                ShutdownPromisePool::callAllPromises();
            });

            $this->container
                ->get(WpFrontController::class)
                ->routing();

        } catch (\Throwable $e) {
            if (!is_null($this->logger)) {
                $this->logger->error($e->getMessage(), [
                    'stacktrace' => $e->getTrace(),
                ]);
            }
        } finally {
            ShutdownPromisePool::callAllPromises();
        }
    }

    /**
     * get IoC Container
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return Logger|null
     */
    public function getLogger(): ?Logger
    {
        return $this->logger;
    }

    /**
     * Accessor to environment vars
     * TODO: Implement env config/system
     * @param string $var
     * @return null|mixed
     */
    public function getEnvVar(string $var)
    {
        return $this->env[$var] ?? null;
    }
}
