<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\Attributes;

use Attribute;

/**
 * Attach one or more RateLimit classes to a controller (applies to every
 * route in it) or to a single method (applies to that route only).
 *
 * #[RateLimit(LoggingRateLimit::class)]
 * #[RateLimit(AuthRateLimit::class)]
 * class UserController { ... }
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class RateLimit {
    public function __construct(public readonly int $maxAttempts = 5, public readonly int $decaySeconds = 60) {}
}
