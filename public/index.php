<?php
//error_reporting(E_ALL);
//ini_set('display_startup_errors', 1);
//ini_set('display_errors', '1');

use App\Http\Action;
use App\Http\Middleware;
use Aura\Router\RouterContainer;
use Framework\Http\Application;
use Framework\Http\Middleware\RouteMiddleware;
use Framework\Http\Router\MiddlewareResolver;
use Framework\Http\Router\AuraRouterAdapter;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\Diactoros\Response;

require dirname(__DIR__) . '/vendor/autoload.php';



### Initialization
$config = require dirname(__DIR__) . '/config/config.local.php';

$pdo = new \PDO($config['db']['dsn'], $config['db']['username'], $config['db']['password']);
$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

$aura = new RouterContainer();
$routes = $aura->getMap();

$routes->get('home', '/', new Action\Task\IndexAction($pdo, new \Framework\View\ViewRenderer()));
$routes->route('create-task', '/task/create', new Action\Task\CreateAction($pdo, new \Framework\View\ViewRenderer()));
$routes->post('update-task-status', '/task/update/status', new Action\Task\UpdateStatusAction($pdo));
$routes->route('update-task', '/task/update', new Action\Task\UpdateDescriptionAction($pdo, new \Framework\View\ViewRenderer()));
$routes->route('login-form', '/login', new Action\LoginAction(new \Framework\View\ViewRenderer()));
$routes->post('logout', '/logout', Action\LogoutAction::class);

$router = new AuraRouterAdapter($aura);

$resolver = new MiddlewareResolver();
$app = new Application($resolver, new Middleware\NotFoundHandler());

$app->pipe(new Middleware\ErrorHandlerMiddleware($config['debug']));
$app->pipe(new RouteMiddleware($router));
$app->pipe(new \Framework\Http\Middleware\DispatchMiddleware($resolver));

### Running

$request = ServerRequestFactory::fromGlobals();
$response = $app->run($request, new Response());
$response = $response->withHeader('Charset', 'UTF-8');

### Sending

$emitter = new SapiEmitter();
$emitter->emit($response);