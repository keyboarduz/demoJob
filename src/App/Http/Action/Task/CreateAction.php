<?php


namespace App\Http\Action\Task;

use App\Model\TaskModel;
use Framework\Db\PdoInstance;
use Framework\View\ViewRenderer;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Http\Helper\Flash;

class CreateAction
{
    private $viewRenderer;
    private $pdo;

    public function __construct(\PDO $pdo, ViewRenderer $viewRenderer)
    {
        $this->pdo = $pdo;
        $this->viewRenderer = $viewRenderer;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $postParams = $request->getParsedBody();

        $task = new TaskModel($this->pdo);

        if ($request->getMethod() === 'POST') {
            $task->name = $postParams['name'] ?? null;
            $task->email = $postParams['email'] ?? null;
            $task->description = $postParams['description'] ?? null;

            if ($task->save()) {
                Flash::set('success', 'Задача успешно добавлена.');
                return new RedirectResponse('/');
            }
        }

        return new HtmlResponse($this->viewRenderer->render('create', [
            'task' => $task,
        ]));
    }

}