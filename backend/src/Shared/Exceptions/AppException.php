<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\Exceptions;

use RuntimeException;

abstract class AppException extends RuntimeException {
    /**
     * @var string $ip - The IP address of the client making the request. It retrieves the value from the 'HTTP_X_FORWARDED_FOR' header if available, otherwise it falls back to 'REMOTE_ADDR'. If neither is set, it defaults to '
     */
    public string $ip;

    abstract public function statusCode(): int;

    public function ip(): string{
        return $this->ip !== '' ? $this->ip : ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '');
    }

    abstract public function headers(): array;
}

