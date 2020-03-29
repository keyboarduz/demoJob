<?php


namespace App\Http\Middleware;


use Framework\View\ViewRenderer;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundHandler
{
    public function __invoke(ServerRequestInterface $request)
    {
        $viewRenderer = new ViewRenderer();

        return new HtmlResponse($viewRenderer->render('404'), 404);
    }
}