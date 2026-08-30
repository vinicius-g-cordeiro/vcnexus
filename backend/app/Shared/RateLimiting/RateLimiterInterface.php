<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\RateLimiting;

interface RateLimiterInterface {
    public function tooManyAttempts(string $key, int $maxAttempts): bool;

    /**
     * Count the number of attempts to the given key.
     * @param string $key
     * @param int $decaySeconds
     * @return void
     */
    public function hit(string $key, int $decaySeconds) : int;

    public function attempts(string $key) : int;

    public function retriesAfter(string $key) : int;

    public function clear(string $key) : void;
}