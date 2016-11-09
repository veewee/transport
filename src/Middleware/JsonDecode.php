<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\Middleware;

use ApiClients\Foundation\Middleware\MiddlewareInterface;
use ApiClients\Foundation\Middleware\PreTrait;
use ApiClients\Foundation\Transport\JsonStream;
use ApiClients\Foundation\Transport\Service\JsonDecodeService as JsonDecodeService;
use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;
use function React\Promise\resolve;
use React\Stream\ReadableStreamInterface;

class JsonDecode implements MiddlewareInterface
{
    use PreTrait;

    /**
     * @var JsonDecodeService
     */
    private $jsonDecodeService;

    /**
     * JsonDecode constructor.
     * @param JsonDecodeService $jsonDecodeService
     */
    public function __construct(JsonDecodeService $jsonDecodeService)
    {
        $this->jsonDecodeService = $jsonDecodeService;
    }

    /**
     * @param ResponseInterface $response
     * @param array $options
     * @return CancellablePromiseInterface
     */
    public function post(ResponseInterface $response, array $options = []): CancellablePromiseInterface
    {
        if ($response->getBody() instanceof ReadableStreamInterface) {
            return resolve($response);
        }

        return $this->jsonDecodeService->handle((string)$response->getBody())->then(function ($json) use ($response) {
            $body = new JsonStream($json);
            return resolve($response->withBody($body));
        });
    }
}