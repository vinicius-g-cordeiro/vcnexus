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
use App\Events\Interfaces\EventListenerInterface;
use Throwable;
use App\Shared\Response;
/**
 * Central event dispatcher.
 *
 * Usage:
 *
 *   // Registration (boot/bootstrap file, or a ServiceProvider-style class):
 *   $events = Container::getInstance();
 *   $events->listen(UserRegistered::class, new SendWelcomeEmailListener());
 *   // or via class-string (lazily instantiated):
 *   $events->listen(UserRegistered::class, SendWelcomeEmailListener::class);
 *
 *   // Firing (inside a controller's store() method, etc.):
 *   Container::getInstance()->dispatch(new UserRegistered($user));
 */
class Container
{
    private static ?Container $instance = null;

    /**
     * @var array<class-string<EventInterface>, array<int, EventListenerInterface|class-string<EventListenerInterface>>>
     */
    private array $listeners = [];

    /**
     * Toggle to true in local/dev to have listener exceptions bubble up
     * instead of being swallowed+logged. Defaults to "fail soft" so that
     * e.g. a broken mail listener never breaks the store() request itself.
     */
    private bool $throwOnListenerError = false;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function throwOnListenerError(bool $throw = true): void
    {
        $this->throwOnListenerError = $throw;
    }

    /**
     * Register a listener instance or class-string for an event.
     *
     * @param class-string<EventInterface> $eventClass
     * @param EventListenerInterface|class-string<EventListenerInterface> $listener
     */
    public function listen(string $eventClass, EventListenerInterface|string $listener): void
    {
        $this->listeners[$eventClass][] = $listener;
    }

    /**
     * Register a listener against every event returned by its own listensTo().
     * Handy so you don't have to repeat the event class at the call site.
     */
    public function subscribe(EventListenerInterface|string $listener): void
    {
        $instance = is_string($listener) ? $this->resolve($listener) : $listener;

        foreach ($instance->listensTo() as $eventClass) {
            $this->listen($eventClass, $listener);
        }
    }

    /**
     * Dispatch an event to all its registered listeners, in registration order.
     */
    public function dispatch(EventInterface $event): void
    {
        $eventClass = $event::class;

        foreach ($this->listeners[$eventClass] ?? [] as $listener) {
            $instance = is_string($listener) ? $this->resolve($listener) : $listener;
            try {
                $instance->handle($event);
            } catch (Throwable $e) {

                Response::log(message:sprintf(
                    '[Events] Listener %s failed handling %s: %s',
                    $instance::class,
                    $eventClass,
                    $e->getMessage()
                ));
                if ($this->throwOnListenerError) {
                    throw $e;
                }
            }
        }
    }

    public function hasListeners(string $eventClass): bool
    {
        return !empty($this->listeners[$eventClass]);
    }

    public function forget(string $eventClass): void
    {
        unset($this->listeners[$eventClass]);
    }

    private function resolve(string $listenerClass): EventListenerInterface
    {
        $instance = new $listenerClass();

        if (!$instance instanceof EventListenerInterface) {
            Response::log(message:sprintf(
                    'Listener class %s must implement EventListenerInterface',
                ));
            throw new \InvalidArgumentException(
                "$listenerClass must implement EventListenerInterface"
            );

        }

        return $instance;
    }
}
