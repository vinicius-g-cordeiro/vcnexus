<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Events;

use App\Events\Interfaces\EventInterface;

/**
 * Base Event class. Extend this for concrete events and just
 * add whatever public properties/payload you need in the constructor.
 *
 * Example:
 *
 *   class UserRegistered extends Event
 *   {
 *       public function __construct(public readonly User $user) {}
 *   }
 */
abstract class Event implements EventInterface
{
    public function getName(): string
    {
        return static::class;
    }
}
