<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared;

use ADOConnection;
use ADODB_Exception;

#[\AllowDynamicProperties]
class Connection {

    protected ?ADOConnection  $connection = null;
    public static ?Connection $instance = null;

    function __construct(?Connection $previousConnection = null){
        $this->init($previousConnection);
    }

    function init(?ADOConnection $dbConnection = null) {
        if($dbConnection == null && (!isset($this->connection) || $this->connection == null)){
            try{
                $connection = ADONewConnection(getenv("DB_DRIVER"));
                $passwd = trim(file_get_contents(getenv("DB_PASSWORD")));
                $connection->PConnect(getenv("DB_HOST"), getenv("DB_USERNAME"), $passwd, getenv("DB_DATABASE"));
                $connection->SetFetchMode(ADODB_FETCH_ASSOC);
                $connection->SetCharSet('utf8');

                $connection->autoCommit = true;
                
            } catch (ADODB_Exception $e) {
                Response::log(file: 'errors', message: $e->getMessage(), status: 500, success: false);
                throw $e;
            }catch (\Exception $e) {
                Response::log(file: 'errors', message: $e->getMessage(), status: 500, success: false);
                throw $e;
            }

            $this->connection = $connection;
        }else{
            // Using pooled connection
            $this->connection = $dbConnection;
        }

        
    }

    static function close() : void {
        self::$connection = null;
        self::$instance = null;
        
    }

    static function getInstance(): Connection {
        if (self::$instance === null) {
            self::$instance = new Connection();
        }
        return self::$instance;
    }

    function getConnection() : ADOConnection {
        if(self::$instance == null){
            self::$instance = new Connection();
        }

        return $this->connection;
    }

    function getConnectionId() : mixed {
        return $this->connection->_connectionID;
    }

    function isConnectionPooled() : bool {
        return $this->connection->ServerInfo()['is_pooled'] === true;
    }

    function isConnected() : bool {
        return $this->connection->isConnected();
    }

    function getConnectionInfo() : object {
        return (object)$this->connection->ServerInfo();
    }

}
