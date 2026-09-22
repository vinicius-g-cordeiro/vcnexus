<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\Domain\Exceptions;

use App\Shared\Exceptions\AppException;

use Throwable;

final class TransactionFailedException extends AppException
{
    public function __construct(string $message = 'Transaction failed!', int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function ip(): string
    {
        return $this->ip;
    }

    public function statusCode(): int
    {
        return 500;
    }

    public function allowedMethods(): array
    {
        return [];
    }

    public function headers(): array
    {
        return [];
    }
}

