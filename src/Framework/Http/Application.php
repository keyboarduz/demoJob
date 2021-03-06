<?php


namespace Framework\Http;


use Framework\Http\Pipeline\Pipeline;
use Framework\Http\Router\MiddlewareResolver;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Application extends Pipeline
{
    private $resolver;
    private $default; 

    public function __construct(MiddlewareResolver $resolver, callable $default)
    {
        parent::__construct();
        $this->resolver = $resolver;
        $this->default = $default;
    }

    public function pipe($middleware): void
    {
        parent::pipe($this->resolver->resolve($middleware));
    }

    public function run(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this($request, $response, $this->default);
    }
}