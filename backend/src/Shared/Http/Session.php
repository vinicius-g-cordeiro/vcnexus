<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\Http;

final class Session
{
    private static ?Session $instance = null;

    private bool $sessionStarted = false;

    public function __construct()
    {
        $this->init();
    }

    public static function getInstance(): Session
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return mixed|array Returns the full $_SESSION array when $key is omitted/empty,
     *                       otherwise the value at that key, or null if it doesn't exist.
     */
    public function get(string $key = ''): mixed
    {
        if ($key === '') {
            return $_SESSION;
        }

        return $_SESSION[$key] ?? null;
    }

    public function set(string $key, mixed $value): mixed
    {
        $_SESSION[$key] = $value;
        return $_SESSION[$key];
    }

    public function remove(string $key): bool
    {
        if (!isset($_SESSION[$key])) {
            return false;
        }

        unset($_SESSION[$key]);
        return !isset($_SESSION[$key]);
    }

    private function init(): bool
    {
        if (session_status() !== PHP_SESSION_NONE) {
            $this->sessionStarted = session_status() === PHP_SESSION_ACTIVE;
            return $this->sessionStarted;
        }

        if (!extension_loaded('redis')) {
            throw new \RuntimeException(
                'The redis PHP extension is required for session handling but is not loaded. '
                . 'Sessions must not silently fall back to file-based storage — see § 6.4 (pgDog conflict).'
            );
        }

        ini_set('session.save_handler', 'redis');
        ini_set('session.save_path', $this->buildRedisDsn());

        session_set_cookie_params([
            'samesite' => 'Lax',
            'httponly' => true,
            'secure' => true,
            'lifetime' => 60 * 60 * 1, // 1 hour
        ]);

        session_start();

        $this->sessionStarted = session_status() === PHP_SESSION_ACTIVE;

        if (!$this->sessionStarted) {
            throw new \RuntimeException('Failed to start a Redis-backed session.');
        }

        return $this->sessionStarted;
    }

    private function buildRedisDsn(): string
    {
        $host = getenv('REDIS_HOST') ?: 'redis';
        $port = getenv('REDIS_PORT') ?: '6379';
        $password = $this->readPasswordFromSecretFile();

        return "tcp://{$host}:{$port}?auth={$password}";
    }

    private function readPasswordFromSecretFile(): string
    {
        $path = getenv('REDIS_PASSWORD');

        if ($path === false || !is_readable($path)) {
            throw new \RuntimeException('REDIS_PASSWORD is not set or not readable.');
        }

        return trim(file_get_contents($path));
    }

    public function isSessionValid(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    public static function has(string $key) : mixed {
        if(isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        return false;
    }
}