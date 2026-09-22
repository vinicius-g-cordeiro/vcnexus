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

class MethodNotAllowedException extends AppException {
    public function __construct(private string $method, private string $path, private array $allowedMethods)
    {
        parent::__construct("405 - Method not allowed: {$method} {$path} ");

    }

    public function statusCode(): int { return 405; }

    public function allowedMethods(): array { return $this->allowedMethods; }

    public function ip() : string { return $this->ip; }

    public function headers(): array {
        return [
            'Allow' => implode(', ', $this->allowedMethods())
        ];
    }
}
