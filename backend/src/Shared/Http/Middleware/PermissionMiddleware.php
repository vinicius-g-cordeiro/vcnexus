<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Shared\Http\Middleware;

use App\Shared\Http\{Request, Response, Session};
use App\Shared\Http\Interfaces\MiddlewareInterface;

class PermissionMiddleware implements MiddlewareInterface
{

    private array $requiredPermissions = [];
    /**
     * 
     * @param Session $session
     */
    public function __construct(private Session $session)
    {
    }
    public function handle(Request $request, ?callable $next = null, ...$arguments): Response
    {
        $this->requiredPermissions = $arguments[0];
        if(isset($this->session) === false) {
            return Response::json(message: '401 Unauthorized Access', data: object())->send(401, [], true);
        }

        $sessionUser = $this->session->get('user');
        
        if(isset($sessionUser) === false){
            return Response::json(message: '401 Unauthorized Access', data: object())->send(401, [], true);
        }

        $hasPermission = array_any($this->requiredPermissions, function ($value, $key) use ($sessionUser) {
            return in_array($value, $sessionUser->permissions);
        });

        if($hasPermission === false){
            return Response::json(message: '401 Unauthorized Access', data: object())->send(401, [], true)->send(401, [], true);
        }

        return $next($request);
    }
}