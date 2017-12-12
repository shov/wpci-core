<?php declare(strict_types=1);

namespace Wpci\Core;

use Monolog\Logger;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Wpci\Core\Facades\RouterStore;
use Wpci\Core\Facades\ShutdownPromisePool;
use Wpci\Core\Helpers\Path;
use Wpci\Core\Helpers\PromisePool;
use Wpci\Core\Helpers\ServiceRegistrator;
use Wpci\Core\Http\WpResponse;
use Wpci\Core\Render\MustacheTemplate;
use Wpci\Core\Render\View;
use Wpci\Core\Contracts\App;
use wpdb;

/**
 * Class Core
 * @package Core
 */
final class Core
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

        $this->path = new Path($rootPath, __DIR__);

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
        $this->exclude(Path::class);

        $this->prepareArguments(WpFrontController::class, new Reference('service_container'));
        $this->prepareArguments(View::class,
            new Reference(MustacheTemplate::class),
            new Reference(WpResponse::class)
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

        $this->container->set(Path::class, $this->path);
        $this->container->set('promise-pool.shutdown', new PromisePool());
        $this->container->compile();

        /**
         * Environment
         * TODO: move to config
         */
        $this->env['testing'] = true;

        \Wpci\Core\Facades\Core::setFacadeRoot($this);
    }

    /**
     * Run the App
     * @param callable $applicationThick
     */
    public function run(callable $applicationThick)
    {
        try {
            /**
             * TODO: remove dependency
             */
            add_action('shutdown', function () {
                ShutdownPromisePool::callAllPromises();
            });

            $applicationThick();

            RouterStore::makeBinding();

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
