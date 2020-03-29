<?php


namespace Tests\App\Http\Action;


use App\Http\Action\HelloAction;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class HelloActionTest extends TestCase
{
    public function testGuest()
    {
        $action = new HelloAction();

        $request = new ServerRequest();

        /** @var ResponseInterface $response */
        $response = $action($request);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('Hello, Guest!', $response->getBody()->getContents());
    }

    public function testJohn()
    {
        $action = new HelloAction();
        $request = (new ServerRequest())->withQueryParams(['name' => 'John']);

        /** @var ResponseInterface $response */
        $response = $action($request);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('Hello, John!', $response->getBody()->getContents());
    }
}