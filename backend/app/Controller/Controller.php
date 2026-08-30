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

use App\Shared\Request;
use App\Shared\Response;
use App\Shared\Connection;
use App\Shared\Session;

class Controller { 
    public function __construct(
        protected ?Request $request = null,
        protected ?Connection $connection = null,
        protected ?Session $session = null
    ) {
        $this->session = Session::getInstance();
    }

    function index() : void {
        Response::json(message: 'Hello World', status: true, code: 200, data: [], bShouldExit: true);
    }
}

