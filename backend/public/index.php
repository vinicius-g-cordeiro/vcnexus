<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

use App\Controller\AuthController;
use App\Controller\Controller;
use App\Controller\UserController;
use App\Shared\Request;
use App\Middleware\LoggingMiddleware;
use App\Shared\Router;

$router = new Router();
$router->addGlobalMiddleware(LoggingMiddleware::class);

$router->registerControllers([
    UserController::class,
    Controller::class,
    AuthController::class
]);

$request = Request::instance();


$router->dispatch($request);
