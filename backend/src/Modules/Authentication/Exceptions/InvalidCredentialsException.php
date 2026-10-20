<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Modules\Authentication\Exceptions;

use App\Shared\Exceptions\AppException;

final class InvalidCredentialsException extends AppException {
    public function __construct(string $message = 'Invalid credentials', int $code = 401, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function ip() : string { return $this->ip; }

    public function statusCode(): int { return 401; }

    public function allowedMethods() : array { return []; }

    public function headers() : array { return []; }
}
