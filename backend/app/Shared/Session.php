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

require_once __DIR__ . '/../../vendor/adodb/adodb-php/session/adodb-session2.php';
require_once __DIR__ . '/../../vendor/adodb/adodb-php/adodb-exceptions.inc.php';
use ADODB_Session;
use App\Shared\Connection;
use App\Shared\Notification;

#[\AllowDynamicProperties]
class Session {

    public static ?Session $instance = null;

    /** @var Notification[] */
    protected ?array $notifications = null;

    protected bool $sessionStarted = false;
    
    function __construct() {
        $this->init();
    }

    public static function getInstance() : Session {
        if(!isset(self::$instance) || self::$instance === null){
            self::$instance = new Session();
        }

        return self::$instance;
    }

    function get(string $key = '') {
        if(isset($key) === false) return $_SESSION;
        if(isset($_SESSION[$key]) === false) return null;
        return $_SESSION[$key] ?? null;
    }

    function set(string $key, $value) {
        $_SESSION[$key] = $value;
        return $_SESSION[$key];
    }

    function remove($key){
        if(!isset($_SESSION[$key])) return;
        unset($_SESSION[$key]);
        return (!isset($_SESSION[$key]));
    }

    function addNotification(?Notification $notification) : void {
        $this->notifications[$notification->type][$notification->UUID] = $notification;
        $_SESSION['notifications'] = $this->notifications;
    }

    function removeNotification(?string $notificationUUID) : void {
        if(!isset($notificationUUID)) return;
        unset($this->notifications[$notificationUUID]);
        $_SESSION['notifications'] = $this->notifications;
    }

    function getNotifications() : ?object {
        return $_SESSION['notifications'] ?? null;
    }

    function init() : bool {
        if(session_status() === PHP_SESSION_NONE){
            
            $password = trim(file_get_contents(getenv('DB_PASSWORD')));
            ADODB_Session::config('pgsql', getenv('DB_HOST'), getenv('DB_USERNAME'), $password , getenv('DB_DATABASE'));
            Connection::getInstance()->getConnection()->Execute("CREATE TABLE IF NOT EXISTS sessions2 (sesskey VARCHAR( 64 ) NOT NULL DEFAULT '', expiry timestamp NOT NULL , expireref VARCHAR( 250 ) DEFAULT '', created timestamp NOT NULL , modified timestamp NOT NULL , sessdata TEXT, PRIMARY KEY ( sesskey ) );");
            ADODB_Session::open('/tmp', 'sessions', null);

            // Set the session cookie parameters
            session_set_cookie_params([
                'samesite' => 'Lax',
                'httponly' => true,
                'secure' => true,
                'path' => '/',
                'lifetime' => 60 * 60 * 2 // 2 hours in seconds
            ]);

            // Start the session
            session_start();
            if(session_status() === PHP_SESSION_ACTIVE){
                $this->sessionStarted = true;
            }
        }
        return $this->sessionStarted;
    }

    public function isSessionValid() : bool {
        return session_status() === PHP_SESSION_ACTIVE;
    }
}