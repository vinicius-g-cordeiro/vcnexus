<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Bootstrap;

use App\Bootstrap\Routing\Router;
use App\Modules\Authentication\Controllers\AuthenticationController;
use App\Modules\Chat\Controllers\ChatController;
use App\Modules\Users\Controllers\UserController;
use App\Modules\Tenant\Controllers\TenantController;
use App\Shared\Http\{Request, Response};

final class Application
{

    protected ?Request $request = null;

    public function __construct(private readonly Router $router)
    {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL ^ E_USER_ERROR ^ E_USER_WARNING ^ E_USER_NOTICE ^ E_DEPRECATED ^ E_USER_DEPRECATED);

        define('APP_PATH', 'var/www/');
        define('PUBLIC_PATH', 'public/');
        define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST']);
        date_default_timezone_set(getenv('APP_TIMEZONE'));
        setlocale(LC_TIME, getenv('APP_LOCALE'), getenv('APP_LOCALE') . '.utf-8', getenv('APP_LOCALE') . '.utf-8');

        $this->router->registerControllers([UserController::class, AuthenticationController::class, TenantController::class, ChatController::class]);
        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline): bool {
            if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED) {
                error_log(sprintf(
                    "[PHP DEPRECATED] %s in %s:%d",
                    $errstr,
                    $errfile,
                    $errline
                ));

                return false;
            }

            if ($errno === E_USER_ERROR) {
                error_log(sprintf(
                    "[PHP USER ERROR] %s in %s:%d",
                    $errstr,
                    $errfile,
                    $errline
                ));

                return false;
            }

            error_log(sprintf(
                "[PHP ERROR] %s in %s:%d",
                $errstr,
                $errfile,
                $errline
            ));

            return false;
        });
    }

    public function run(): void
    {
        $this->request = new Request();
        try {
            $this->router->dispatch($this->request);
        } catch (\Throwable $th) {
            // Get a valid HTTP status code not in the 200-399 range, for example avoiding the ADODB error codes which endup throwing 502 bad gateway, so we won't know the real error
            $code = $th->getCode();
            if ($code < 400) {
                $code = 500;
                // log the real error
                @error_log(sprintf("%s - %s %s", __METHOD__, $th->getMessage(), $th->getTraceAsString()), 1);
            } else {
                $code = $th->getCode();
            }
            Response::json(data: null, message: $th->getMessage())->send($code, [], true);
        }
    }
}