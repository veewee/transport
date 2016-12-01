<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport\Service;

use ApiClients\Foundation\Service\ServiceInterface;
use function ExceptionalJSON\encode;
use React\EventLoop\LoopInterface;
use React\Promise\CancellablePromiseInterface;
use function React\Promise\resolve;
use function WyriHaximus\React\futureFunctionPromise;

final class JsonEncodeService implements ServiceInterface
{
    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    /**
     * @param $input
     * @return CancellablePromiseInterface
     */
    public function handle($input): CancellablePromiseInterface
    {
        if (!is_array($input)) {
            return resolve($input);
        }

        return futureFunctionPromise($this->loop, $input, function ($json) {
            return encode($json);
        });
    }
}
