<?php declare(strict_types=1);

namespace Wpci\Core;

use Closure;
use Monolog\Logger;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Dotenv\Dotenv;
use Wpci\Core\Facades\RouterStore;
use Wpci\Core\Facades\ShutdownPromisePool;
use Wpci\Core\Helpers\Path;
use Wpci\Core\Helpers\PromisePool;
use Wpci\Core\Helpers\ServiceRegistratorTrait;
use Wpci\Core\Http\WpResponse;
use Wpci\Core\Render\MustacheTemplate;
use Wpci\Core\Render\View;
use wpdb;

/**
 * Class Core
 * @package Core
 */
final class Core
{
    use ServiceRegistratorTrait;

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

        (new Dotenv())->load($this->path->getProjectRoot('/.env'));

        $errorLogFile = getenv('ERROR_LOG');
        if (false === $errorLogFile) $errorLogFile = '/error.log.wp.txt';
        ini_set('error_log', $this->path->getProjectRoot($errorLogFile));

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
         */
        $this->env = getenv() || [];

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
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Accessor to environment vars
     * @param string $var
     * @param $default
     * @return null|mixed
     */
    public function getEnvVar(string $var, $default)
    {
        $value = $this->env[$var] ?? null;

        if ($value === false) {
            return $default instanceof Closure ? $default() : $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (
            strlen($value) > 1
            && (0 === strpos($value, '"'))
            && (strlen($value) - 1 === strpos($value, '"'))
        ) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
