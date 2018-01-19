<?php declare(strict_types=1);

namespace Wpci\Core\Flow;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Toolkit to help to make registration services  in the container
 * using as source all found classes in given directories
 */
class ServiceRegistrator
{
    /** @var array */
    protected $serviceArguments = [];

    /** @var array */
    protected $excluded = [
        ContainerManager::class,
        self::class,
    ];

    /** @var string */
    protected $rootDir;

    /** @var string */
    protected $baseNamespace;

    /** @var ContainerBuilder */
    protected $container;

    /**
     * ServiceRegistrator constructor.
     * @param string $rootDir
     * @param string $baseNamespace
     * @param ContainerBuilder $container
     */
    public function __construct(string $rootDir, string $baseNamespace, ContainerBuilder $container)
    {
        $this->rootDir = $rootDir;
        $this->container = $container;
        $this->baseNamespace = $baseNamespace;
    }

    /**
     * Add arguments to entity before register it
     * @param string $id
     * @param Reference[] ...$arguments
     */
    public function prepareArguments(string $id, Reference ...$arguments)
    {
        if(!isset($this->serviceArguments[$id])) $this->serviceArguments[$id] = [];
        $this->serviceArguments[$id] = array_merge($this->serviceArguments[$id], $arguments);
    }

    /**
     * Count names of classes which should be excluded
     * @param string[] ...$ids
     */
    public function exclude(string ...$ids)
    {
        $this->excluded = array_merge($this->excluded, $ids);
    }

    /**
     * Register all entities (only classes) from directory
     * @param string $subDir
     * @throws \Exception
     */
    public function walkDirForServices(string $subDir) {
        $entities = [];
        $entitiesPath = $this->rootDir . '/' . ($subDir);

        $entitiesDir = new \DirectoryIterator($entitiesPath);
        foreach ($entitiesDir as $fileInfo) {
            if (!$fileInfo->isFile() || ('php' !== $fileInfo->getExtension())) {
                continue;
            }

            $expectedClassName = $fileInfo->getBasename('.' . $fileInfo->getExtension());

            $file = new \SplFileObject($fileInfo->getPathname());
            $content = $file->fread($file->getSize());
            $aTokens = token_get_all($content);
            $count = count($aTokens);
            for ($i = 2; $i < $count; $i++) {
                if ((T_CLASS === $aTokens[$i - 2][0]) && (T_WHITESPACE === $aTokens[$i - 1][0]) && (T_STRING === $aTokens[$i][0])) {
                    $foundClassName = $aTokens[$i][1];
                    if ($expectedClassName === $foundClassName) $entities[] = $foundClassName;
                }
            }
            $file = null;
        }

        foreach ($entities as $entity) {
            $className = $this->baseNamespace . $subDir . "\\" . $entity;

            if(in_array($className, $this->excluded)) {
                continue;
            }

            $bound = $this->container->register($className);

            if(!empty($this->serviceArguments[$className])) {
                foreach ($this->serviceArguments[$className] as $argument) {
                    $bound = $bound->addArgument($argument);
                }
            }

            $bound->setPublic(true)->setAutowired(true);
        }
    }

    /**
     * Back the container instance
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }
}