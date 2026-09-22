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

final class ValidationException extends AppException
{
    /** @param array<string, string[]> $errors field => messages */
    public function __construct(private array $errors)
    {
        parent::__construct('Validation failed.');
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function error(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    function ip(): string
    {
        return $this->ip;
    }

    function statusCode(): int
    {
        return 422;
    }

    function headers(): array {
        return [];
    }
}