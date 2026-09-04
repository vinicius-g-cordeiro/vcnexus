<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Events\Container;
use App\Events\Listeners\UserLoggedInListener;
use App\Events\Listeners\UserRegisteredListener;
use App\Events\Auth\UserRegistered;
use App\Events\Auth\UserLoggedIn;


ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL ^E_WARNING ^E_DEPRECATED );

// Always check the CORS
require_once __DIR__ . '/Middleware/CorsMiddleware.php';

$events = Container::getInstance();

// Laravel-style: map event => [listeners]
$eventListenerMap = [
    UserRegistered::class => [
        UserRegisteredListener::class,
    ],
    UserLoggedIn::class => [
        UserLoggedInListener::class,
    ]
];

foreach ($eventListenerMap as $eventClass => $listeners) {
    foreach ($listeners as $listener) {
        $events->listen($eventClass, $listener);
    }
}
