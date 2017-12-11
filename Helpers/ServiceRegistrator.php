<?php declare(strict_types=1);

namespace Wpci\Core\Helpers;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Trait ServiceRegistrator
 * @package Wpci\Core\Helpers
 *
 * required in implementation
 * @property ContainerBuilder $container
 */
trait ServiceRegistrator
{
    /**
     * @var array
     */
    protected $serviceArguments= [];

    /**
     * Add arguments to entity before register it
     * @param string $id
     * @param array ...$arguments
     */
    protected function prepareArguments(string $id, ...$arguments)
    {
        $this->serviceArguments[$id] = is_array($this->serviceArguments[$id])
            ? array_merge($this->serviceArguments[$id], $arguments)
            : $arguments;
    }

    /**
     * Register all entities (only classes) from directory
     * @param string $dirInCore
     */
    protected function walkDirForServices(string $dirInCore) {
        $entities = [];
        $entitiesPath = __DIR__ . '/' . ($dirInCore);

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
            $bound = $this->container->register($className);

            if(!empty($this->serviceArguments[$className])) {
                foreach ($this->serviceArguments[$className] as $argument) {
                    $bound = $bound->addArgument($argument);
                }
            }
        }
    }
}