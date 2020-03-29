<?php

namespace Tests\Framework\Http\Router;


use App\Http\Middleware\NotFoundHandler;
use Framework\Http\Router\MiddlewareResolver;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareResolverTest extends TestCase
{
    /**
     * @dataProvider getValidHandlers
     * @param $handler
     */
    public function testDirect($handler)
    {
        $resolver = new MiddlewareResolver();
        $middleware = $resolver->resolve($handler);

        /** @var Response $response */
        $response = $middleware(
            (new ServerRequest())->withAttribute('attribute', $value = 'value'),
            new Response(),
            new NotFoundHandler()
        );

        self::assertEquals([$value], $response->getHeader('X-Header'));
    }

    /**
     * @dataProvider getValidHandlers
     * @param $handler
     */
    public function testNext($handler)
    {
        $resolver = new MiddlewareResolver();
        $middleware = $resolver->resolve($handler);

        /** @var ResponseInterface $response */
        $response = $middleware(
            (new ServerRequest())->withAttribute('next', true),
            new Response(),
            new NotFoundHandler()
        );

        self::assertEquals(404, $response->getStatusCode());
    }

    public function testArray()
    {
        $resolver = new MiddlewareResolver();

        $middleware = $resolver->resolve([
            new DummyMiddleware(),
            new CallableMiddleware()
        ]);

        /** @var ResponseInterface $response */
        $response = $middleware(
            (new ServerRequest())->withAttribute('attribute', $value = 'value'),
            new Response(),
            new NotFoundMiddleware()
        );

        self::assertEquals(['dummy'], $response->getHeader('X-Dummy'));
        self::assertEquals([$value], $response->getHeader('X-Header'));
    }

    public function getValidHandlers()
    {
        return [
            'Callable Callback' => [function (ServerRequestInterface $request, callable $next){
                if ($request->getAttribute('next')) {
                    return $next($request);
                }
                return (new HtmlResponse(('')))
                    ->withHeader('X-Header', $request->getAttribute('attribute'));
            }],
            'Callable Class' => [CallableMiddleware::class],
            'Callable Object' => [new CallableMiddleware()],
            'DoublePass Callback' => [function (ServerRequestInterface $request, ResponseInterface $response, callable $next) {
                if ($request->getAttribute('next')) {
                    return $next($request);
                }

                return $response
                    ->withHeader('X-Header', $request->getAttribute('attribute'));
            }],
            'DoublePass Class' => [DoublePassMiddleware::class],
            'DoublePass Object' => [new DoublePassMiddleware()],
            'Interop Class' => [InteropMiddleware::class],
            'Interop Object' => [new InteropMiddleware()]
        ];
    }
}

class CallableMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        if ($request->getAttribute('next')) {
            return $next($request);
        }

        return (new HtmlResponse(''))
            ->withHeader('X-Header', $request->getAttribute('attribute'));
    }

}

class DoublePassMiddleware
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        if ($request->getAttribute('next')) {
            return $next($request);
        }

        return $response
            ->withHeader('X-Header', $request->getAttribute('attribute'));
    }
}

class InteropMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getAttribute('next')) {
            return $handler->handle($request);
        }

        return (new HtmlResponse(''))
            ->withHeader('X-Header', $request->getAttribute('attribute'));
    }
}

class NotFoundMiddleware
{
    public function __invoke(ServerRequestInterface $request)
    {
        return new EmptyResponse(404);
    }
}

class DummyMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        return $next($request)
            ->withHeader('X-Dummy', 'dummy');
    }
}

