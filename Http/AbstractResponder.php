<?php declare(strict_types=1);

namespace Wpci\Core\Http;

use Wpci\Core\Contracts\ResponseInterface;
use Wpci\Core\Exceptions\NotFoundHttpException;
use Wpci\Core\Exceptions\UserAuthException;
use Wpci\Core\Exceptions\ValidationException;

/**
 * The base of all responding classes, contain useful methods to  wrap
 * risky logic, make auto fail response, and other
 */
abstract class AbstractResponder
{
    /**
     * @param \Throwable $e
     * @param null|string $message
     * @param int $status
     * @return ResponseInterface
     * @throws \Exception
     */
    protected function failWithException(\Throwable $e, ?string $message = null, int $status = RegularResponse::HTTP_FORBIDDEN): ResponseInterface
    {
        if (is_null($message)) {
            $message = $e->getMessage();
        }

        return $this->makeResponse($status, $message, $e);
    }

    /**
     * @param string $message
     * @param int $status
     * @return ResponseInterface
     * @throws \Exception
     */
    protected function failWithAuth(string $message = "Authorization required!", int $status = RegularResponse::HTTP_UNAUTHORIZED): ResponseInterface
    {
        return $this->makeResponse($status, $message);
    }

    /**
     * @param string $message
     * @param int $status
     * @return ResponseInterface
     * @throws \Exception
     */
    protected function failWithNotFound(string $message = "Not found", int $status = RegularResponse::HTTP_NOT_FOUND): ResponseInterface
    {
        return $this->makeResponse($status, $message);
    }

    /**
     * @param $content
     * @param int $status
     * @return ResponseInterface
     * @throws \Exception
     */
    protected function success($content, int $status = RegularResponse::HTTP_OK): ResponseInterface
    {
        return $this->makeResponse($status, $content);
    }

    /**
     * E.g. wrapping regular controller action into try...catch block, give the process as callback,
     *
     * @param callable $process
     * @param mixed|null $successSpecResult
     * @return ResponseInterface
     * @throws \Exception
     */
    protected function wrap(callable $process, $successSpecResult = null): ResponseInterface
    {
        try {
            $result = $process();

        } catch (ValidationException $e) {
            return $this->failWithException($e);

        } catch (UserAuthException $e) {
            return $this->failWithAuth();

        } catch (NotFoundHttpException $e) {
            return $this->failWithNotFound();

        } catch (\Throwable $e) {
            return $this->failWithException($e);
        }

        if(!is_null($successSpecResult)) {
            $result = $successSpecResult;
        }

        if ($result instanceof ResponseInterface) {
            return $result;
        }

        return $this->success($result);
    }

    /**
     * Common method to build response
     * @param int $status
     * @param $content
     * @param null|\Throwable $e
     * @return ResponseInterface
     */
    abstract protected function makeResponse(int $status, $content, ?\Throwable $e = null): ResponseInterface;
}