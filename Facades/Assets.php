<?php declare(strict_types=1);

namespace Wpci\Core\Facades;

use Wpci\Core\Helpers\AbstractFacade;

/**
 * The facade for @see \Wpci\Core\Render\Assets
 *
 * @method static $this registerStyle(string $key, ?string $path = null, array $deps = [], ?string $ver = null)
 * @method static \Wpci\Core\Render\Assets registerFooterScript(string $key, ?string $path = null, array $deps = [], ?string $ver = null)
 * @method static \Wpci\Core\Render\Assets registerHeaderScript(string $key, ?string $path = null, array $deps = [], ?string $ver = null)
 * @method static \Wpci\Core\Render\Assets addVariableToScript(string $key, string $name, $value = null)
 */
class Assets extends AbstractFacade
{

    /**
     * Return the facade root object
     * @return mixed
     * @throws \Exception
     * @throws \Error
     */
    public static function getFacadeRoot()
    {
        return Core::get(\Wpci\Core\Render\Assets::class);
    }
}