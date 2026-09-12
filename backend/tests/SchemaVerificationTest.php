<?php
/**
 * @brief Verifies the application schema metadata used to build PostgreSQL tables.
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0
 * @date 2026/09/12
 */

declare(strict_types=1);

namespace Tests;

use App\Database\Schema\Schema;
use App\Database\Schema\TenantUsersSchema;
use App\Database\Schema\UsersSchema;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

final class SchemaVerificationTest extends TestCase
{
    /** @brief Ensures user schemas expose the columns required by persistence and tenancy. */
    public function testUserSchemaColumns(): void
    {
        $userColumns = Schema::columns(new UsersSchema());
        $tenantUserColumns = Schema::columns(new TenantUsersSchema());

        self::assertSame('users', (new UsersSchema())->table);
        self::assertSame('tenant_users', (new TenantUsersSchema())->table);
        self::assertArrayHasKey('email', $userColumns);
        self::assertArrayHasKey('password', $userColumns);
        self::assertArrayHasKey('tenant_id', $userColumns);
        self::assertArrayHasKey('user_id', $tenantUserColumns);
        self::assertArrayHasKey('tenant_id', $tenantUserColumns);
    }

    /** @brief Ensures user uniqueness and tenant foreign-key rules remain declared. */
    public function testUserSchemaConstraints(): void
    {
        $uniqueConstraints = Schema::constraints(new UsersSchema());
        $tenantConstraints = Schema::constraints(new TenantUsersSchema());

        self::assertCount(3, $uniqueConstraints);
        self::assertContains('uq_users_email', array_map(
            static fn (object $constraint): string => $constraint->name,
            $uniqueConstraints
        ));
        self::assertContains('uq_users_username', array_map(
            static fn (object $constraint): string => $constraint->name,
            $uniqueConstraints
        ));
        self::assertContains('fk_users_tenant', array_map(
            static fn (object $constraint): string => $constraint->name,
            $tenantConstraints
        ));
    }
}
