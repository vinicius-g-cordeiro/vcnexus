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
use App\Shared\Session;
use App\Shared\Interfaces\MiddlewareInterface;
use App\Shared\Request;
use App\Shared\Connection;
use App\Shared\Response;

class OwnerMiddleware  implements MiddlewareInterface {

    protected ?Session $session;
    protected ?AuthService $authService;
    public function __construct() {
        $this->session = Session::getInstance();
        $this->authService = new AuthService(Connection::getInstance());
    }
    public function handle(Request $request, callable $next): mixed {

        if(isset($this->session) === false) {
            throw new AppExceptionHandler(message: 'There was no session initialized!', code: 500);
        }

        $userSession = $this->session->get('user');

        $user = $this->authService->getUser($request->post('uuid'));

        
        if(isset($user, $userSession) === false){
            Response::json(message: '403 Unauthorized Access', status: false, code: 403, data: object(), bShouldExit: true);
        }

        if($user->uuid !== $userSession->uuid){
            Response::json(message: '403 Unauthorized Access', status: false, code: 403, data: object(), bShouldExit: true);
        }

        return $next($request);
    }
}