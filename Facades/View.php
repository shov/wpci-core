<?php declare(strict_types=1);

namespace Wpci\Core\Facades;

use Wpci\Core\Contracts\ResponseInterface;
use Wpci\Core\Helpers\AbstractFacade;
use Wpci\Core\Http\RegularResponse;

/**
 * The facade for @see \Wpci\Core\Render\View
 *
 * @method static ResponseInterface display(string $key, array $data, int $status = RegularResponse::HTTP_OK)
 */
class View extends AbstractFacade
{

    /**
     * Return the facade root object
     * @return mixed
     * @throws \Exception
     * @throws \Error
     */
    public static function getFacadeRoot()
    {
        return Core::get(\Wpci\Core\Render\View::class);
    }
}