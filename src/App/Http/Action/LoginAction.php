<?php


namespace App\Http\Action;


use App\Model\UserModel;
use Framework\View\ViewRenderer;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LoginAction
{
    private $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if (!UserModel::isGuest()) {
            return new RedirectResponse('/');
        }

        if ($request->getMethod() === 'POST' && UserModel::load($request->getParsedBody())) {

            if (UserModel::login()) {

                return new RedirectResponse('/');
            }

        }

        return new HtmlResponse($this->viewRenderer->render('login', [
            'errors' => UserModel::getErrors()
        ]));
    }
}