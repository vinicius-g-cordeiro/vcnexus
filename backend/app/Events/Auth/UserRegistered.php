<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Events\Auth;

use App\Events\Event;

/**
 * Fired after a user is successfully persisted during registration.
 * Keep this a plain, immutable data carrier — no logic here.
 */
class UserRegistered extends Event
{
    public function __construct(
        public readonly int $userId,
        public readonly string $name,
        public readonly string $email,
    ) {
    }
}
