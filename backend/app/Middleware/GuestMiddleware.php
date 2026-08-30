<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Exceptions\UnauthorizedUserExceptionHandler;
use App\Shared\Request;
use App\Shared\Session;
use App\Shared\Response;
use App\Shared\Interfaces\MiddlewareInterface;
use App\Shared\Notification;

final class GuestMiddleware implements MiddlewareInterface {
    public function handle(Request $request, callable $next) : mixed {
        $session = Session::getInstance();
        // If the user is already logged in
        // Show the notification and then show the response json with status code 401 - Unauthorized
        if($session->get('auth.user') !== null) {
            Response::notification(new Notification(message: 'You are already logged in', data: object(), type: 'error', icon: 'error', iconColor: 'red'));
            throw new UnauthorizedUserExceptionHandler(message: 'You are already logged in', code: 401, previous: null);
        }

        return $next($request);
    }
}

