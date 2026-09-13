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

use App\Exceptions\AppExceptionHandler;
use App\Service\AuthService;
use App\Shared\Attributes\Permissions as PermissionsAttribute;
use App\Shared\Session;
use App\Shared\Interfaces\MiddlewareInterface;
use App\Shared\Request;
use App\Shared\Connection;
use App\Shared\Response;

class PermissionsMiddleware  implements MiddlewareInterface {

    protected ?Session $session;
    protected ?AuthService $authService;
    public function __construct(protected ?PermissionsAttribute $necessaryPermissions = null) {
        $this->session = Session::getInstance();
        $this->authService = new AuthService(Connection::getInstance()->getConnection());
    }
    public function handle(Request $request, callable $next): mixed {

        if(isset($this->session) === false) {
            throw new AppExceptionHandler(message: 'There was no session initialized!', code: 500);
        }

        $sessionUser = $this->session->get('user');
        
        if(isset($sessionUser) === false){
            Response::json(message: '403 Unauthorized Access', status: false, code: 403, data: object(), bShouldExit: true);
        }

        $hasPermission = array_any($this->necessaryPermissions->permissions, function ($value, $key) use ($sessionUser) {
            return in_array($value, $sessionUser->permissions);
        });

        if($hasPermission === false){
            Response::json(message: '403 Unauthorized Access', status: false, code: 403, data: object(), bShouldExit: true);
        }

        return $next($request);
    }
}