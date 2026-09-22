<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Infrastructure\Database;


final class ConnectionFactory
{
    public function create(): ?\ADOConnection
    {
        $db = \ADONewConnection(getenv('DB_DRIVER'));
        $db->Connect(argHostname: $this->host(),argUsername: $this->username(),argPassword: $this->password(),argDatabaseName: $this->database());
        $db->autoCommit = false;
        $db->enableLastInsertID(true);
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
        $db->autoRollback =  true;
        return $db;
    }

    public function createSuperUser(string $user, string $password): ?\ADOConnection
    {
        $db = \ADONewConnection(getenv('DB_DRIVER'));
        $db->Connect(argHostname: 'db',argUsername: trim($user),argPassword: trim($password),argDatabaseName: 'app_db', forceNew:true);
        $db->raiseErrorFn = function($db, $code, $msg, $sqlstate, $raw){ error_log('Error: ' . $msg); return false; };
        $db->autoCommit = false;
        $db->enableLastInsertID(true);
        $db->IgnoreErrors(false);
        $db->LogSQL(true);
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
        $db->autoRollback =  true;
        return $db;
    }

    private function host(): string
    {
        return getenv('DB_HOST') . ':' . getenv('PGDOG_PORT');
    }
    private function username(): string
    {
        return getenv('DB_USERNAME');
    }
    private function database(): string
    {
        return getenv('PGDOG_DATABASE_NAME');
    }

    private function password(): string
    {
        $passwdFile = getenv('DB_PASSWORD'); // e.g. /run/secrets/db_app_password (Docker secret mount path
        $pasww= trim(file_get_contents($passwdFile));
        return $pasww;
    }


}