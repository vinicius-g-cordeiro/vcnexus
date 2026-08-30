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

    private ?object $params, $get, $post, $server, $files, $cookie;
    public $uri, $path, $method, $routeParams, $headers, $body;
 
    public function __construct(?object $params = null) {
        if(!isset(self::$instance)) {
            self::$instance = $this;
        }

        $this->params = (object)array_filter($_REQUEST, 'is_string', ARRAY_FILTER_USE_KEY);
        $this->get = (object)array_filter($_GET, 'is_string', ARRAY_FILTER_USE_KEY);
        $this->post = (object)array_filter($_POST, 'is_string', ARRAY_FILTER_USE_KEY);
        $this->server = (object)array_filter($_SERVER, 'is_string', ARRAY_FILTER_USE_KEY);
        $this->files = (object)array_filter($_FILES, 'is_string', ARRAY_FILTER_USE_KEY);

        $this->uri = $_SERVER['REQUEST_URI'];
        $this->path = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function fromGlobals() : Request{
        $headers = [];
                foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headers[str_replace('_', '-', substr($key, 5))] = (string) $value;
            }
        }
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['CONTENT-TYPE'] = (string) $_SERVER['CONTENT_TYPE'];
        }
 
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $path = '/' . trim((string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/'), '/');
 
        $body = $_POST;
        $contentType = $headers['CONTENT-TYPE'] ?? '';
        if ($body === [] && str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input') ?: '';
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $body = $decoded;
            }
        }
 
        
        $this->headers = $headers;
        $this->method = $method;
        $this->path = $path;
        $this->body = $body;
 
        return $this;
    }

    public static function instance(): Request {
        if(!isset(self::$instance)) {
            self::$instance = new Request();
        }
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

    public function ip() {
        return $this->server->HTTP_X_FORWARDED_FOR ?? ($this->server->REMOTE_ADDR ?? '0.0.0.0');
    }

    public static function all() : array {
        return [
            'params' => self::$instance->params,
            'get' => self::$instance->get,
            'post' => self::$instance->post,
            'server' => self::$instance->server,
            'files' => self::$instance->files,
            'uri' => self::$instance->uri,
            'path' => self::$instance->path,
            'method' => self::$instance->method,
            'headers' => self::$instance->headers,
            'body' => self::$instance->body
        ];   
    }
}