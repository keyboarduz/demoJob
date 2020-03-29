<?php


namespace App\Http\Action;


use App\Model\UserModel;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LogoutAction
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            UserModel::logout();

            return new RedirectResponse('/');
        }

        return new RedirectResponse('/', 401);
    }
}