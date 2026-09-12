<?php 
/**
 * @abstract 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace Tests;

use App\Shared\Connection;

require_once __DIR__ . '/../vendor/autoload.php';

class InitialTest extends \PHPUnit\Framework\TestCase
{
    /** @var Connection|null */
    protected $connection = null;

    /** @var Session|null */
    protected $session = null;

    private function databaseIsConfigured(): bool
    {
        $passwordFile = getenv('DB_PASSWORD');

        return getenv('DB_DRIVER') !== false
            && getenv('DB_HOST') !== false
            && getenv('DB_USERNAME') !== false
            && getenv('DB_DATABASE') !== false
            && is_string($passwordFile)
            && is_readable($passwordFile);
    }

    /** @brief Verifies that the configured database connection is available. */
    public function testConnection(): void
    {
        if (!$this->databaseIsConfigured()) {
            $this->markTestSkipped('Database environment is not configured for integration tests.');
        }

        $this->connection = Connection::getInstance();
        $this->assertTrue($this->connection->isConnected(), 'Connection is not initialized! Test failed!');
    }

}