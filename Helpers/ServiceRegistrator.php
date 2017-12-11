<?php declare(strict_types=1);

namespace Wpci\Core\Helpers;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Trait ServiceRegistrator
 * @package Wpci\Core\Helpers
 *
 * required in implementation
 * @property ContainerBuilder $container
 * @property Path $path
 */
trait ServiceRegistrator
{
    /**
     * @var array
     */
    protected $serviceArguments = [];

    /**
     * @var array
     */
    protected $excluded = [];

    /**
     * Add arguments to entity before register it
     * @param string $id
     * @param Reference[] ...$arguments
     */
    protected function prepareArguments(string $id, Reference ...$arguments)
    {
        if(!isset($this->serviceArguments[$id])) $this->serviceArguments[$id] = [];
        $this->serviceArguments[$id] = array_merge($this->serviceArguments[$id], $arguments);
    }

    /**
     * Count names of classes which should be excluded
     * @param string[] ...$ids
     */
    protected function exclude(string ...$ids)
    {
        $this->excluded = array_merge($this->excluded, $ids);
    }

    /**
     * Register all entities (only classes) from directory
     * @param string $dirInCore
     * @throws \Exception
     */
    protected function walkDirForServices(string $dirInCore) {
        $entities = [];
        $entitiesPath = $this->path->getCorePath() . '/' . ($dirInCore);

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
            $className = "Wpci\\Core\\" . $dirInCore . "\\" . $entity;

            if(in_array($className, $this->excluded)) {
                continue;
            }

            $bound = $this->container->register($className);

            if(!empty($this->serviceArguments[$className])) {
                foreach ($this->serviceArguments[$className] as $argument) {
                    $bound = $bound->addArgument($argument);
                }
            }

            $bound->setPublic(true);
        }
    }
}