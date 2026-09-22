<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Shared\Http\Controllers;


use App\Shared\Http\{Request, Session};


abstract class BaseController
{
    public Request $request;
    public Session $session;
    public function __construct(Request $request, Session $session) {
        $this->request = $request;
        $this->session = $session;
    }
}