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

class Request {

    static private Request $instance;

    private static ?object $params, $get, $post, $server, $files;
 
    public function __construct(?object $params = null) {
        if(!isset(self::$instance)) {
            self::$instance = $this;
        }

        self::$params = (object)array_filter($_REQUEST, 'is_string', ARRAY_FILTER_USE_KEY);
        self::$get = (object)array_filter($_GET, 'is_string', ARRAY_FILTER_USE_KEY);
        self::$post = (object)array_filter($_POST, 'is_string', ARRAY_FILTER_USE_KEY);
        self::$server = (object)array_filter($_SERVER, 'is_string', ARRAY_FILTER_USE_KEY);
        self::$files = (object)array_filter($_FILES, 'is_string', ARRAY_FILTER_USE_KEY);
    }

    public static function instance(): Request {
        return self::$instance;
    }

    public static function initialize() : Request {
        return self::$instance = new Request();
    }

    public static function params(): object {
        return self::$params;
    }

    public static function get(): object {
        return self::$get;
    }

    public function _get(string $key = '', $value = '') : object|null {
        if(empty($key)) {
            return self::$get;
        }
        if(empty($value)) {
            return self::$get->{$key} ?? object();
        } else {
            self::$get->{$key} = $value;
        }

        return self::$get;
    }

    public static function post(): object {
        return self::$post;
    }

    public function _post(string $key = '', $value = '') : object|null {
        if(empty($key)) {
            return self::$post;
        }
        if(empty($value)) {
            return self::$post->{$key} ?? object();
        } else {
            self::$post->{$key} = $value;
        }

        return self::$post;
    }


    public static function files(): object {
        return self::$files;
    }
}