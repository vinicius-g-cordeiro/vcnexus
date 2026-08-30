<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Controller;

use App\Shared\Attributes\Route;
use App\Shared\Response;
use App\Shared\Request;
use App\Service\UserService;
use App\Shared\Attributes\RateLimit;

#[Route('GET', '/users/')]
class UserController extends Controller
{

    protected ?UserService $service = null; 
    public function __construct(){

    }

    #[Route('GET', '/list/')]
    #[RateLimit(maxAttempts: 5, decaySeconds: 60)]
    public function index(?Request $request) : void {
        Response::json(message: 'Placeholder index', status: true, code: 200, data: object());

    }

    #[Route('GET', '/{id}/')]
    public function show(string $id) : void {
        Response::json(message: 'Placeholder data', status: true, code: 200, data: object());
    }


    #[Route('POST|POST', '/create/')]
    #[RateLimit(maxAttempts: 5, decaySeconds: 60)]
    public function store(Request $request) : void {
        Response::json(message: 'Placeholder store', status: true, code: 200, data: $request->all());
    }

    #[Route('PUT', '/{id}/update/')]
    public function update(Request $request, string $id) : void {
        Response::json(message: 'Placeholder update', status: true, code: 200, data: $request->all());
    }

    #[Route('DELETE', '/{id}/delete/')]
    public function deactivate(string $id) : void {
        Response::json(message: 'Placeholder deactivate', status: true, code: 200, data: object());
    }

    #[Route('PUT|GET|PATCH', '/{id}/activate/')]
    public function activate(string $id) : void {
        Response::json(message: 'Placeholder activate', status: true, code: 200, data: object());
    }

    #[Route('PUT|GET|PATCH', '/{id}/block/')]
    public function block(string $id) : void {
        Response::json(message: 'Placeholder block', status: true, code: 200, data: object());
    }



}