<?php


namespace App\Http\Action\Task;


use App\Model\TaskModel;
use Framework\View\ViewRenderer;
use JasonGrimes\Paginator;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexAction
{
    private $pdo;
    private $viewRenderer;

    public function __construct(\PDO $pdo, ViewRenderer $viewRenderer)
    {
        $this->pdo = $pdo;
        $this->viewRenderer = $viewRenderer;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $page = intval($queryParams['page'] ?? 1);
        $sortAttribute = $queryParams['sort'] ?? null;

        $taskModel = new TaskModel($this->pdo);

        $countTask = $taskModel->getCountTask();

        $totalItems = $countTask;
        $itemsPerPage = 2;
        $currentPage = $page;
        $urlPattern = '/?page=(:num)';
        if ($sortAttribute !== null) {
            $urlPattern .= '&sort=' . $sortAttribute;
        }

        $tasks = $taskModel->search($queryParams, $itemsPerPage);


        $pagination = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);

        return new HtmlResponse($this->viewRenderer->render('index', [
            'tasks' => $tasks,
            'pagination' => $pagination,
            'sortAttribute' => $sortAttribute,
        ]));
    }
}