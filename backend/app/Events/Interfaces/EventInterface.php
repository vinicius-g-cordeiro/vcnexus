<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Events\Interfaces;

/**
 * Marker interface for all Events.
 * Events are simple data carriers - no behavior, just payload.
 */
interface EventInterface
{
    /**
     * Unique name used to register/look up listeners.
     * Convention: return static::class, but allow override
     * (e.g. for wildcard-style names like "user.*").
     */
    public function getName(): string;
}
