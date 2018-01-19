<?php declare(strict_types=1);

namespace Wpci\Core\Http\Drops;

use Wpci\Core\Contracts\ActionInterface;
use Wpci\Core\Contracts\ResponseInterface;
use Wpci\Core\Http\RegularResponse;

/**
 * The Action
 */
class Action implements ActionInterface
{
    protected $reference;

    /**
     * @inheritdoc
     */
    public function __construct($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @inheritdoc
     */
    public function call(...$arguments): ResponseInterface
    {
        $callback = $this->getCallbackFromReference($this->reference);

        $response = call_user_func($callback, ...$arguments);

        if(!is_object($response)) {
            return new RegularResponse($response);
        }

        $responseImplements = class_implements($response);
        if(false === $responseImplements || !in_array(ResponseInterface::class, $responseImplements)) {
            $response = new RegularResponse($response);
        }
        return $response;
    }

    /**
     * Call this when action calling, to make Controller in a time
     * @param $reference
     * @return callable
     */
    protected function getCallbackFromReference($reference): callable
    {
        $this->reference = $reference;
        if(is_callable($reference)) {
            if(!is_string($reference)) {
                $callback = $reference;
            } else {
                $parts = explode("::", $reference);
                $callback = [new $parts[0](), $parts[1]];
            }
        } else {
            $callback = function () use ($reference) {
                new RegularResponse($reference);
            };
        }

        return $callback;
    }
}