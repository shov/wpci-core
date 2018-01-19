<?php declare(strict_types=1);

namespace Wpci\Core\Render;

use Wpci\Core\Flow\PromiseManager;
use Wpci\Core\Wordpress\WpProvider;

/**
 * Register assets (js, css, images...)
 * in wordpress to output them on site pages in head/footer
 */
class Assets
{
    /** @var WpProvider */
    protected $wpProvider;

    /** @var PromiseManager */
    protected $promiseManager;

    /**
     * DI
     * @param WpProvider $wpProvider
     * @param PromiseManager $promiseManager
     */
    public function __construct(WpProvider $wpProvider, PromiseManager $promiseManager)
    {
        $this->wpProvider = $wpProvider;
        $this->promiseManager = $promiseManager;
    }

    /**
     * @param string $key
     * @param null|string $path
     * @param array|null $deps
     * @param null|string $ver
     * @return Assets
     * @throws \ErrorException
     */
    public function registerStyle(
        string $key,
        ?string $path = null,
        array $deps = [],
        ?string $ver = null): Assets
    {
        $this->promiseManager
            ->addPromise('wp_enqueue_scripts', function () use ($key, $path, $deps, $ver) {
                if (is_null($path)) {
                    $this->wpProvider->wpEnqueueStyle($key);
                } else {
                    (empty($ver)) ? $ver = false : $ver = '?' . $ver;

                    $this->wpProvider->wpRegisterStyle($key, $path, $deps, $ver);
                    $this->wpProvider->wpEnqueueStyle($key);
                }
            });

        return $this;
    }

    /**
     * @param string $key
     * @param null|string $path
     * @param array|null $deps
     * @param null|string $ver
     * @return Assets
     * @throws \ErrorException
     */
    public function registerFooterScript(
        string $key,
        ?string $path = null,
        array $deps = [],
        ?string $ver = null): Assets
    {
        $this->promiseManager
            ->addPromise('wp_enqueue_scripts', function () use ($key, $path, $deps, $ver) {
                if (is_null($path)) {
                    $this->wpProvider->wpEnqueueScript($key);
                } else {
                    (empty($ver)) ? $ver = false : $ver = '?' . $ver;

                    $this->wpProvider->wpRegisterScript($key, $path, $deps, $ver, true);
                    $this->wpProvider->wpEnqueueScript($key);
                }
            });

        return $this;
    }

    /**
     * @param string $key
     * @param null|string $path
     * @param array|null $deps
     * @param null|string $ver
     * @return Assets
     * @throws \ErrorException
     */
    public function registerHeaderScript(
        string $key,
        ?string $path = null,
        array $deps = [],
        ?string $ver = null): Assets
    {
        $this->promiseManager
            ->addPromise('wp_enqueue_scripts', function () use ($key, $path, $deps, $ver) {
                if (is_null($path)) {
                    $this->wpProvider->wpEnqueueScript($key);
                } else {
                    (empty($ver)) ? $ver = false : $ver = '?' . $ver;

                    $this->wpProvider->wpRegisterScript($key, $path, $deps, $ver, false);
                    $this->wpProvider->wpEnqueueScript($key);
                }
            });

        return $this;
    }

    /**
     * @param string $key
     * @param string $name
     * @param null $value
     * @return Assets
     * @throws \ErrorException
     */
    public function addVariableToScript(string $key, string $name, $value = null): Assets
    {
        if (empty($key) || empty($name)) {
            throw new \InvalidArgumentException("Wrong name of script or variable to register!");
        }

        $this->promiseManager
            ->addPromise('wp_enqueue_scripts', function () use ($key, $name, $value) {
                $this->wpProvider->wpLocalizeScript($key, $name, $value);
            });

        return $this;
    }
}