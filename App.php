<?php declare(strict_types=1);

namespace Wpci\Core;

use Monolog\Formatter\HtmlFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wpci\Core\Facades\ShutdownPromisePool;
use Wpci\Core\Helpers\Path;
use Wpci\Core\Helpers\PromisePool;
use Wpci\Core\Helpers\Singleton;
use Wpci\Core\Http\WpFrontController;
use wpdb;

/**
 * Class App
 * @package App
 */
final class App
{
    /** @var ContainerBuilder */
    protected $container;

    /** @var Path */
    protected $path;

    /** @var array */
    protected $env = [];

    /**
     * App constructor. Bootstrap
     * @param string $rootPath
     * @throws \Exception
     */
    private function __construct(string $rootPath)
    {
        $this->container = new ContainerBuilder();

        /**
         * Logs
         */
        $this->path = new Path($rootPath);

        /** @var Logger $logger */
        $logger = new Logger('general');

        $debugLog = $this->path->getProjectRoot('/debug.log.html');
        file_put_contents($debugLog, ''); //Cleaning TODO: move to config

        $debugHandler = new StreamHandler($debugLog, $logger::DEBUG);
        $debugHandler->setFormatter(new HtmlFormatter());
        $logger->pushHandler($debugHandler);

        $errorLog = $this->path->getProjectRoot('/error.log.html');
        file_put_contents($errorLog, ''); //Cleaning TODO: move to config

        $errorHandler = new StreamHandler($errorLog, $logger::ERROR);
        $errorHandler->setFormatter(new HtmlFormatter());
        $logger->pushHandler($errorHandler);

        $this->container->set('Logger', $logger);

        ini_set('error_log', $this->path->getProjectRoot('/error.log.wp.txt'));

        /**
         * Wordpress
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
         * Config
         */
        $serviceConfigLoader = new YamlFileLoader($this->container, new FileLocator($this->path->getConfigPath()));
        $serviceConfigLoader->load('services.yaml');

        $this->container->set('promise-pool.shutdown', new PromisePool());
        $this->container->compile();

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
            add_action('shutdown', function () {
                ShutdownPromisePool::callAllPromises();
            });

            $this->container
                ->get(WpFrontController::class)
                ->routing();

        } catch (\Throwable $e) {
            try {
                /** @var Logger $logger */
                $logger = $this->container->get('Logger');

            } catch (\Throwable $e) {
                ShutdownPromisePool::callAllPromises();
                die("Can't get the Logger");
            }

            $logger->error($e->getMessage(), [
                'stacktrace' => $e->getTrace(),
            ]);
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
