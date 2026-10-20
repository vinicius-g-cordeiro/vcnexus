<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Modules\Chat\Exceptions;

use App\Shared\Exceptions\AppException;

/**
 * Chat errors carry their own HTTP status code:
 *  - 400/422 invalid input
 *  - 403     no access to the room
 *  - 500     could not persist
 *
 * Do NOT use 401 for these: the frontend axios interceptor redirects to /login on any 401.
 */
final class ChatException extends AppException {
    public function __construct(string $message = 'Chat error', int $code = 400, ?\Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function ip() : string { return $this->ip; }

    public function statusCode(): int { return $this->getCode() >= 400 ? $this->getCode() : 400; }

    public function allowedMethods() : array { return []; }

    public function headers() : array { return []; }
}