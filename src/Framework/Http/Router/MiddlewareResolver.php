<?php


namespace Framework\Http\Router;


use Framework\Http\Pipeline\Pipeline;
use Framework\Http\Pipeline\InteropHandlerWrapper;
use Framework\Http\Pipeline\UnknownMiddlewareTypeException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class MiddlewareResolver
{
    public function resolve($handler): callable
    {
        if ( \is_array($handler) ) {
            return $this->createPipe($handler);
        }

        if (is_string($handler)) {
            return function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use ($handler) {
                $middleware = $this->resolve(new $handler());
                return $middleware($request, $response, $next);
            };
        }

        if ($handler instanceof MiddlewareInterface) {
            return function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use ($handler) {
                return $handler->process($request, new InteropHandlerWrapper($next));
            };
        }

        if (\is_object($handler)) {
            $reflection = new \ReflectionObject($handler);
            if ($reflection->hasMethod('__invoke')) {
                $method = $reflection->getMethod('__invoke');
                $parameters = $method->getParameters();
                if (\count($parameters) === 2 && $parameters[1]->isCallable()) {
                    return function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use ($handler) {
                        return $handler($request, $next);
                    };
                }
                return $handler;
            }
        }

        throw new UnknownMiddlewareTypeException($handler);
    }

    protected function createPipe(array $handler): Pipeline
    {
        $pipeline = new Pipeline();
        foreach ($handler as $item) {
            $pipeline->pipe($this->resolve($item));
        }

        return $pipeline;
    }
}