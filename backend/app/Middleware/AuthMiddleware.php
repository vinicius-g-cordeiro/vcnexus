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
use App\Shared\Session;
use App\Shared\Interfaces\MiddlewareInterface;
use App\Shared\Request;
use App\Shared\Response;

final class AuthMiddleware  implements MiddlewareInterface {

    protected ?Session $session;
    public function __construct() {
        $this->session = Session::getInstance();
    }
    public function handle(Request $request, callable $next): mixed {
        if(isset($this->session) === false) {
            throw new AppExceptionHandler(message: 'There was no session initialized!', code: 500);
        }

        $user = $this->session->get('user');
        if(isset($user) === false){
            Response::json(message: '403 Unauthorized Access', status: false, code: 403, data: object());
        }
        
        return $next($request);
    }
}