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
final class TenantContext
{
    private static ?string $tenantId = null;
    private static ?string $userId = null;
    private static ?string $slug = null;

    public static function set(string $tenantId, ?string $userId = null, ?string $slug = null): void
    {
        self::$tenantId = $tenantId;
        self::$userId = $userId;
        self::$slug = $slug;
    }

    public static function tenantId(): ?string
    {
        return self::$tenantId;
    }

    public static function userId(): ?string
    {
        return self::$userId;
    }

    public static function isResolved(): bool
    {
        return self::$tenantId !== null;
    }
}