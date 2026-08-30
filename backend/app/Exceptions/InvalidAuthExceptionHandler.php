<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;
use App\Shared\Response;

final class InvalidAuthExceptionHandler extends \Exception {
    function __construct(string $message, int $code = 500, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        Response::log(file: 'error', message: $message, status: $code, success: false);
    }
}