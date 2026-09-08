<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\Context;
final class AuthContext
{
    private static ?string $userId = null;
    private static ?string $tenant_id = null;
    private static ?string $email = null;
    private static array $roles = [];
    private static bool $resolved = false;

    public static function set(string $userId, string $tenant_id, ?string $email = null, array $roles = []): void
    {
        self::$userId = $userId;
        self::$email = $email;
        self::$roles = $roles;
        self::$tenant_id = $tenant_id;
        self::$resolved = true;
    }

    public static function userId(): ?string
    {
        return self::$userId;
    }

    public static function tenantId(): ?string
    {
        return self::$tenant_id;
    }

    public static function email(): ?string
    {
        return self::$email;
    }

    public static function hasRole(string $role): bool
    {
        return in_array($role, self::$roles, true);
    }

    public static function isAuthenticated(): bool
    {
        return self::$resolved;
    }

    public static function clear(): void
    {
        self::$userId = null;
        self::$email = null;
        self::$tenant_id = null;
        self::$roles = [];
        self::$resolved = false;
    }
}