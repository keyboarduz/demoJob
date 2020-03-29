<?php


namespace App\Http\Action\Task;


use App\Model\TaskModel;
use App\Model\UserModel;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UpdateStatusAction
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() !== 'POST') {
            return new RedirectResponse('/');
        }

        if (UserModel::isGuest()) {
            return new RedirectResponse('/');
        }

        $postData = $request->getParsedBody();

        $task = new TaskModel($this->pdo);

        if ($task->updateStatus($postData['id'], TaskModel::STATUS_SUCCESS)) {
            return new RedirectResponse('/');
        }

        return new RedirectResponse('/');
    }
}