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
 * Attach one or more middleware classes to a controller (applies to every
 * route in it) or to a single method (applies to that route only).
 *
 * #[Middleware(LoggingMiddleware::class)]
 * #[Middleware(AuthMiddleware::class)]
 * class UserController { ... }
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Middleware {
    public function __construct(public string $class) {}
}
