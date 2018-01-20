<?php declare(strict_types=1);

namespace Wpci\Core\Contracts;

use Symfony\Component\Dotenv\Dotenv;
use Wpci\Core\Core;
use Wpci\Core\Facades\Path;

/**
 * Use it as the base for you application
 */
abstract class AbstractApp implements AppInterface
{
    /** @var string */
    protected $appRoot;

    /** @var Core */
    protected $core;

    /** @var ?callable */
    protected $beforeRun = null;

    public function __construct(string $appRoot)
    {
        $this->appRoot = $appRoot;
    }

    /**
     * Getting the core to handle it
     * @param Core $core
     */
    public function handle(Core $core)
    {
        $this->core = $core;

        $this->setPath();
        $this->setUpEnvironment();
        $this->setUpServices();

        if(!is_null($this->beforeRun)) {
            call_user_func_array($this->beforeRun, [$this->core]);
        }

        $this->core->run([$this, 'run']);
    }

    /**
     * Will be called after setup but before run
     * @param callable $callback
     * @return AppInterface
     */
    public function beforeRun(callable $callback): AppInterface
    {
        $this->beforeRun = $callback;
        return $this;
    }

    /**
     * Waiting for the core who call it at the time
     */
    abstract public function run();

    /**
     * Set the Path, important method, call it before any another one
     */
    protected function setPath()
    {
        $this->core
            ->setPath($this->appRoot);
    }

    /**
     * Load .env if it set and merge with environment
     */
    protected function setUpEnvironment()
    {
        $dotEnv = new Dotenv();
        $dotEnv->populate($this->getEnvironmentVars());

        $envFilePath = Path::getProjectRoot('/.env');
        if (is_readable($envFilePath)) {
            $dotEnv->load($envFilePath);
        }

        $errorLogFile = getenv('ERROR_LOG');

        /**
         * Wordpress use it to log down self errors
         */
        ini_set('error_log',Path::getProjectRoot($errorLogFile));
    }

    protected function setUpServices()
    {
        //
    }

    /**
     * Redefine with your default env loader
     * @return array
     */
    protected function getEnvironmentVars(): array
    {
        return [
            'ERROR_LOG' => '/error.log.wp.txt',
        ];
    }
}