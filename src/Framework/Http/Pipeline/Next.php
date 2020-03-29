<?php


namespace Framework\Http\Pipeline;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Next
{
    private $queue;
    private $next;
    private $response;

    public function __construct(\SplQueue $queue, ResponseInterface $response, callable $next)
    {
        $this->queue = $queue;
        $this->next = $next;
        $this->response = $response;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->queue->isEmpty()) {
            return ($this->next)($request);
        }

        $middleware = $this->queue->dequeue();

        return $middleware($request, $this->response, function(ServerRequestInterface $request) {
            return $this($request);
        });
    }
}