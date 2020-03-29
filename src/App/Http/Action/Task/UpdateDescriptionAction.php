<?php


namespace App\Http\Action\Task;


use App\Model\TaskModel;
use App\Model\UserModel;
use Framework\View\ViewRenderer;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UpdateDescriptionAction
{
    private $pdo;
    private $viewRenderer;

    public function __construct(\PDO $pdo, ViewRenderer $viewRenderer)
    {
        $this->pdo = $pdo;
        $this->viewRenderer = $viewRenderer;
    }

    public function __invoke(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        if (UserModel::isGuest()) {
            return new RedirectResponse('/login');
        }
        $queryParams = $request->getQueryParams();
        $postData = $request->getParsedBody();

        $newTask = new TaskModel($this->pdo);
        $taskModel = $newTask->findById(isset($queryParams['id']) ? $queryParams['id'] : $postData['id']);

        if (!$taskModel) {
            return $next($request);
        }

        if ($request->getMethod() === 'POST') {

            $newTask->id = isset($postData['id']) ? $postData['id'] : null;
            $newTask->description= isset($postData['description']) ? $postData['description'] : null;

            if ($result = $newTask->updateDescription($postData['id'])){
                return new RedirectResponse('/');
            }

//            var_dump($newTask->getErrors()); exit;

        }

        return new HtmlResponse($this->viewRenderer->render('update', [
            'taskModel' => $taskModel,
            'errors' => $newTask->getErrors(),
        ]));
    }

}