<?php

namespace Tests;

use ADOConnection;
use PHPUnit\Framework\TestCase;
use App\Infrastructure\Database\ConnectionFactory;

use function PHPUnit\Framework\assertTrue;

final class tests extends TestCase
{

    public ?ADOConnection $connection = null;

    function setUp(): void{
        parent::setUp();        

        $connectionFactory = new ConnectionFactory();
        $this->connection = $connectionFactory->create();
    }

    public function tearDown(): void{
        parent::tearDown();
    }

    public function testConnection(): void {
        $this->assertTrue($this->connection->isConnected(), 'Database connection should be established');
    }

    public function testUserTables(): void {
        $this->assertTrue($this->connection->isConnected(), 'Database connection should be established');
        $tables = $this->connection->MetaTables();
        $this->assertContainsEquals('user_credentials', $tables, 'The "user_credentials" table should exist in the database');
        $this->assertContainsEquals('user_profile', $tables, 'The "user_profile" table should exist in the database');
        $this->assertContainsEquals('user_consents', $tables, 'The "user_consents" table should exist in the database');
        $this->assertContainsEquals('user_sensitive', $tables, 'The "user_sensitive" table should exist in the database');
    }   

    public function testReferenceTables(): void {
        $this->assertTrue($this->connection->isConnected(), 'Database connection should be established');
        $tables = $this->connection->MetaTables();
        $this->assertContainsEquals('countries', $tables, 'The "countries" table should exist in the database');
        $this->assertContainsEquals('states', $tables, 'The "states" table should exist in the database');
        $this->assertContainsEquals('cities', $tables, 'The "cities" table should exist in the database');
        $this->assertContainsEquals('subscription_statuses', $tables, 'The "subscription_statuses" table should exist in the database');
        $this->assertContainsEquals('subscription_types', $tables, 'The "subscription_types" table should exist in the database');
        $this->assertContainsEquals('user_roles', $tables, 'The "user_roles" table should exist in the database');
        $this->assertContainsEquals('contact_types', $tables, 'The "contact_types" table should exist in the database');
        $this->assertContainsEquals('legal_basis', $tables, 'The "legal_basis" table should exist in the database');
        $this->assertContainsEquals('marital_statuses', $tables, 'The "marital_statuses" table should exist in the database');
        $this->assertContainsEquals('genders', $tables, 'The "genders" table should exist in the database');
        $this->assertContainsEquals('sexual_orientations', $tables, 'The "sexual_orientations" table should exist in the database');
        $this->assertContainsEquals('ethnicity', $tables, 'The "ethnicity" table should exist in the database');
        $this->assertContainsEquals('disabilities', $tables, 'The "disabilities" table should exist in the database');
    }
}
