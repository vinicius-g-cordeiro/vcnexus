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

interface EventListenerInterface
{
    /**
     * Handle the event.
     */
    public function handle(EventInterface $event): void;

    /**
     * Which event classes this listener should be attached to.
     * Returning multiple lets one listener subscribe to several events.
     *
     * @return array<class-string<EventInterface>>
     */
    public function listensTo(): array;
}
