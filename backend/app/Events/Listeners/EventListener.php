<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Events\Listeners;

use App\Events\Interfaces\EventInterface;
use App\Events\Interfaces\EventListenerInterface;
use App\Shared\Response;

/**
 * Optional convenience base class for listeners.
 * Not required (listeners only need to implement EventListenerInterface),
 * but gives you a `handle()`-only feel similar to Laravel if you only
 * ever listen to a single event class - set $event in the child class.
 */
abstract class EventListener implements EventListenerInterface
{
    /** @var class-string<EventInterface>|null */
    protected ?string $event = null;

    public function listensTo(): array
    {
        if ($this->event === null) {
            Response::log(message:sprintf('%s must be set $event or override listensTo()', static::class));
            throw new \LogicException(
                static::class . ' must set $event or override listensTo().'
            );
        }

        return [$this->event];
    }

    abstract public function handle(EventInterface $event): void;
}
