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
use App\Shared\Session;

require_once __DIR__ . "../../app/bootstrap.php";

class InitialTest extends \PHPUnit\Framework\TestCase
{
    protected $connection = null;

    protected $session = null;
    function __construct() {
        parent::__construct();

        $this->connection = Connection::getInstance();
        $this->session = Session::getInstance();
        
    }
    public function testConnection() {
        $this->assertTrue($this->connection->isConnected(), 'Connection is not initialized! Test failed!');
    }

    public function testSession() {
        $this->assertTrue($this->session->isSessionValid(), 'Session is not initialized! Test failed!');
    }

}