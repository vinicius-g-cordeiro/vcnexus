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

use App\Shared\RateLimiting\RateLimiterInterface;

use RuntimeException;

/**
 * Fixed window rate limiter with no external dependencies 
 */
final class FileRateLimiter implements RateLimiterInterface {

    function __construct(private readonly string $storagePath = __DIR__ . '/../../../storage/rate_limits.json') {
        $dir = dirname($this->storagePath);
        if(!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Unable to create directory "%s"', $dir));
        }

        if(!file_exists($this->storagePath)) {
            file_put_contents($this->storagePath, json_encode(object()));
        }
    }

    function hit(string $key, int $decaySeconds) : int {
        return $this->mutate(function(array $data) use ($key, $decaySeconds) : array {
            $now = time();
            $entry = $data[$key] ?? ['count' => 0, 'resetAt' => $now + $decaySeconds];

            if($entry['resetAt'] <= $now) {
                $entry = ['count' => 0, 'resetAt' => $now + $decaySeconds];
            }

            $entry['count']++;
            $data[$key] = $entry;

            return [$data, $entry['count']];
        });
    }

    function attempts(string $key) : int {
        return $this->mutate(function(array $data) use ($key) : array {
            $entry = $data[$key] ?? null;
            if($entry === null || $entry['resetAt'] <= time()) {
                return [$data, 0];
            }

            return [$data, $entry['count']];
        });
    }

    function tooManyAttempts(string $key, int $maxAttempts) : bool {
        return $this->attempts($key) >= $maxAttempts;
    }


    function retriesAfter(string $key) : int {
        return $this->mutate(function(array $data) use ($key) : array {
            $entry = $data[$key] ?? null;
            if($entry === null){
                return [$data, 0];
            }

            return [$data, max(0, $entry['resetAt'] - time())];
        });
    }


    function clear(string $key) : void {
        $this->mutate(function(array $data) use ($key) : array {
            unset($data[$key]);
            return [$data, null];
        });
    }

    /**
     * Opens the store, locks it, lets the callback read + return updated data plus a result
     * writes data back, then unlocks. Keeps every read-modify-write in this class atomic under concurrent requests
     * 
     * @param callable(array<string, mixed>): array{0: array<string ,mixed>, 1: mixed} $fn
     */
    private function mutate (callable $fn) : mixed {
        $handle = fopen($this->storagePath, 'c+');
        if($handle === false) {
            throw new RuntimeException(sprintf('Unable to open rate limiting storage "%s"', $this->storagePath));
        }

        try {
            flock($handle, LOCK_EX);

            $contents = stream_get_contents($handle);
            $data = $contents !== false && $contents !== '' ? (json_decode($contents, true) ?: []) : [];

            [$newData, $result] = $fn($data);
            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, json_encode($newData, JSON_PRETTY_PRINT));
            fflush($handle);

            return $result;
        }finally{
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}