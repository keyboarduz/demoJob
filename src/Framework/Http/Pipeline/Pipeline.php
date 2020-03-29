<?php


namespace Framework\Http\Pipeline;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Pipeline
{
    /** @var \SplQueue $queue */
    private $queue;

    public function __construct()
    {
        $this->queue = new \SplQueue();
    }

    public function pipe($middleware): void
    {
        $this->queue->enqueue($middleware);
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next): ResponseInterface
    {
        $delegate = new Next(clone $this->queue, $response, $next);

        return $delegate($request);
    }
}