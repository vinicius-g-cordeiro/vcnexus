<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Bootstrap\Routing\Exceptions;

use App\Shared\Exceptions\AppException;

class RouteNotFoundException extends AppException {
    public function __construct(private string $method, private string $path) { 
        parent::__construct("404 - Route not found: {$method} {$path}"); 
    }
    public function statusCode(): int { return 404; }

    public function ip() : string { return $this->ip; }

    public function headers() : array { return []; }
}