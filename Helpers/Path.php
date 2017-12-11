<?php declare(strict_types=1);

namespace Wpci\Core\Helpers;

use WP;

/**
 * Class Path, all important pathes in the application
 * @package Wpci\Core
 */
class Path
{
    /**
     * @var string
     */
    protected $root;

    /**
     * @var string
     */
    protected $core;

    /**
     * Path constructor.
     * @param string $root
     * @throws \Exception
     */
    public function __construct(string $root, string $core)
    {
        if(empty($root) || !is_dir($root)) {
            throw new \Exception("Invalid root dir");
        }
        $this->root = $root;

        if(empty($core) || !is_dir($core)) {
            throw new \Exception("Invalid core dir");
        }
        $this->core = $core;
    }

    /**
     * Get absolute project directory path
     * @param string $tail
     * @return string
     * @throws \Exception
     */
    public function getProjectRoot(string $tail = ''): string
    {
        if(empty($this->root) || !is_dir($this->root)) {
            throw new \Exception("Invalid root dir or not set yet!");
        }

        return realpath($this->root) . $tail;
    }

    /**
     * Get absolute path to config folder
     * @param string $tail
     * @return string
     * @throws \Exception
     */
    public function getConfigPath(string $tail = ''): string
    {
        return $this->getProjectRoot() . '/config' . $tail;
    }

    /**
     * Get absolute path to wordpress folder
     * @param string $tail
     * @return string
     * @throws \Exception
     */
    public function getWpPath(string $tail = ''): string
    {
        return $this->getProjectRoot() . '/wordpress' . $tail;
    }

    /**
     * Get absolute path to Core folder
     * @param string $tail
     * @return string
     * @throws \Exception
     */
    public function getCorePath(string $tail = ''): string
    {
        return $this->core;
    }

    /**
     * Get absolute path to Application folder
     * @param string $tail
     * @return string
     * @throws \Exception
     */
    public function getAppPath(string $tail = ''): string
    {
        return $this->getSrcPath() . '/app' . $tail;
    }

    /**
     * Get absolute path to Application templates folder
     * @param string $tail
     * @return string
     * @throws \Exception
     */
    public function getTplPath(string $tail = ''): string
    {
        return $this->getSrcPath() . '/app/templates' . $tail;
    }

    /**
     * Get absolute path to source folder
     * @param string $tail
     * @return string
     * @throws \Exception
     */
    public function getSrcPath(string $tail = ''): string
    {
        return $this->getProjectRoot() . '/src' . $tail;
    }

    /**
     * Get current url with WP global query
     * @return string
     * @throws \Exception
     */
    public function getCurrentUrl(): string
    {
        /** @var WP $wp */
        $wp = \Wpci\Core\Facades\App::get('wp');
        return home_url(add_query_arg([], $wp->request));
    }

    /**
     * Get WP theme directory URI
     * @param string $tail
     * @return string
     */
    public function getWpThemeUri(string $tail = ''): string
    {
        return get_template_directory_uri() . $tail;
    }

    /**
     * Get public css folder URI
     * @param string $tail
     * @return string
     */
    public function getCssUri(string $tail = ''): string
    {
        return $this->getWpThemeUri('/css' . $tail);
    }

    /**
     * Get public js folder URI
     * @param string $tail
     * @return string
     */
    public function getJsUri(string $tail = ''): string
    {
        return $this->getWpThemeUri('/js' . $tail);
    }

    /**
     * Get public images folder URI
     * @param string $tail
     * @return string
     */
    public function getImagesUri(string $tail = ''): string
    {
        return $this->getWpThemeUri('/images' . $tail);
    }

    /**
     * Get public fonts folder URI
     * @param string $tail
     * @return string
     */
    public function getFontsUri(string $tail = ''): string
    {
        return $this->getWpThemeUri('/fonts' . $tail);
    }
}