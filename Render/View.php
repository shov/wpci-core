<?php declare(strict_types=1);

namespace Wpci\Core\Render;

use Wpci\Core\Contracts\ResponseInterface;
use Wpci\Core\Contracts\TemplateInterface;
use Wpci\Core\Facades\Core;
use Wpci\Core\Http\RegularResponse;

/**
 * The view: makes content in given (or generated) response object with given template strategy
 */
class View
{
    protected $templateStrategy;

    protected $responseStrategy;

    /**
     * View constructor.
     * @param TemplateInterface $templateStrategy
     * @param null|ResponseInterface $responseStrategy
     * @throws \Error
     * @throws \Exception
     */
    public function __construct(TemplateInterface $templateStrategy, ?ResponseInterface $responseStrategy = null)
    {
        $this->templateStrategy = $templateStrategy;
        $this->responseStrategy = $responseStrategy ?? Core::get(RegularResponse::class);
    }

    /**
     * Make templating and give the response
     * @param string $key
     * @param array $data
     * @param int $status
     * @return RegularResponse
     */
    public function display(string $key, array $data, int $status = RegularResponse::HTTP_OK): ResponseInterface
    {
        $content = $this->templateStrategy->render($key, $data);

        $this->responseStrategy->setContent($content);
        $this->responseStrategy->setStatusCode($status);
        return $this->responseStrategy;
    }
}