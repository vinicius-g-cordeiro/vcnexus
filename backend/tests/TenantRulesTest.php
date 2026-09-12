<?php
/**
 * @brief Verifies row-level tenant isolation rules declared by the tenant schema.
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0
 * @date 2026/09/12
 */

declare(strict_types=1);

namespace Tests;

use App\Database\Schema\Schema;
use App\Database\Schema\TenantUsersSchema;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

final class TenantRulesTest extends TestCase
{
    /** @brief Verifies that tenant access policies cover all write operations and isolation. */
    public function testTenantPolicies(): void
    {
        $policies = Schema::policies(new TenantUsersSchema());
        $names = array_map(static fn (object $policy): string => $policy->name, $policies);

        self::assertSame(
            ['tenant_membership', 'tenant_isolation', 'tenant_update_isolation', 'tenant_insert_isolation', 'tenant_delete_isolation'],
            $names
        );

        foreach ($policies as $policy) {
            self::assertSame('tenant_users', $policy->table);
            self::assertSame('app_user', $policy->toUser);
            self::assertArrayHasKey('', $policy->using);
        }

        self::assertArrayHasKey('', $policies[1]->withCheck);
        self::assertArrayHasKey('', $policies[2]->withCheck);
        self::assertArrayHasKey('', $policies[3]->withCheck);
        self::assertArrayHasKey('', $policies[4]->withCheck);
    }

    /** @brief Verifies that PostgreSQL row-level security is enabled and forced for tenant users. */
    public function testTenantRowLevelSecurity(): void
    {
        $rls = Schema::rls(new TenantUsersSchema());

        self::assertCount(2, $rls);
        self::assertTrue($rls[0]->forced);
        self::assertFalse($rls[1]->forced);
        self::assertSame('tenant_users', $rls[0]->table);
        self::assertSame('tenant_users', $rls[1]->table);
    }
}
