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

    public ?object $params, $get, $post, $server, $files, $cookie, $patch, $put, $delete;
    public $uri, $path, $method, $routeParams, $headers;
 
    public function __construct(?object $params = null) {
        if(!isset(self::$instance)) {
            self::$instance = $this;
        }
        
        $this->params = (object)filter_var_array($_REQUEST, FILTER_SANITIZE_SPECIAL_CHARS | FILTER_SANITIZE_STRING);
        $this->get = (object)filter_var_array($_GET, FILTER_SANITIZE_SPECIAL_CHARS | FILTER_SANITIZE_STRING);
        $this->post = (object)filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS | FILTER_SANITIZE_STRING);
        $this->server = (object)filter_var_array($_SERVER, FILTER_SANITIZE_SPECIAL_CHARS | FILTER_SANITIZE_STRING);
        $this->files = (object)filter_var_array($_FILES, FILTER_SANITIZE_SPECIAL_CHARS | FILTER_SANITIZE_STRING);

        $this->uri = $_SERVER['REQUEST_URI'];
        $this->path = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];

        $this->post = $this->get_request_body('POST');
        $this->get = $this->get_request_body('GET');
        $this->patch = $this->get_request_body('PATCH');
        $this->put = $this->get_request_body('PUT');


        $this->method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $this->path = '/' . trim((string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/'), '/');

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $this->headers[str_replace('_', '-', substr($key, 5))] = (string) $value;
            }
        }
    }


    function get_request_body(string $inMethod = 'POST') : ?object {
        $method = $_SERVER['REQUEST_METHOD'];
        $input = file_get_contents('php://input');
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if($method != $inMethod){ return null; } // If the method is not the same we just return as it should be null

        switch($method){
            case 'PATCH':
                if(str_contains($contentType, 'application/json')){
                    $this->patch = (object)filter_var_array(json_decode($input, true), FILTER_SANITIZE_SPECIAL_CHARS | FILTER_SANITIZE_STRING);
                    return $this->patch;
                }
                parse_str($input, $data);
                $this->patch = (object)$data;
                return $this->patch;
            case 'PUT':
                if(str_contains($contentType, 'application/json')){
                    $this->put = (object)filter_var_array(json_decode($input, true), FILTER_SANITIZE_SPECIAL_CHARS | FILTER_SANITIZE_STRING);
                    return $this->put;
                }
                parse_str($input, $data);
                $this->put = (object)$data;
                return $this->put;
            case 'DELETE':
                if(str_contains($contentType, 'application/json')){
                    $this->delete = (object)filter_var_array(json_decode($input, true), FILTER_SANITIZE_SPECIAL_CHARS | FILTER_SANITIZE_STRING);
                    return $this->delete;
                }
                parse_str($input, $data);
                $this->delete = (object)$data;
                return $this->delete;
            case 'POST':
                if(str_contains($contentType, 'application/json')){
                    $this->post = (object)filter_var_array(json_decode($input, true), FILTER_SANITIZE_SPECIAL_CHARS | FILTER_SANITIZE_STRING);
                    return $this->post;
                }
                parse_str($input, $data);
                $this->post = (object)array_merge($data, filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS | FILTER_SANITIZE_STRING));
                return $this->post;
            default: 
                break;
        }

        return null;
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

    public function put(string $key = '', $value = '') : mixed {
        if(empty($key)) {
            return $this->put;
        }
        if(empty($value)) {
            return $this->put->{$key} ?? object();
        } else {
            $this->put->{$key} = $value;
        }

        return $this->put;
    }

    public function patch(string $key = '', $value = '') : mixed {
        if(empty($key)) {
            return $this->patch;
        }
        if(empty($value)) {
            return $this->patch->{$key} ?? object();
        } else {
            $this->patch->{$key} = $value;
        }

        return $this->patch;
    }

    public function delete(string $key = '', $value = '') : mixed {
        if(empty($key)) {
            return $this->delete;
        }
        if(empty($value)) {
            return $this->delete->{$key} ?? object();
        } else {
            $this->delete->{$key} = $value;
        }

        return $this->delete;
    }

    public function get(string $key = '', $value = '') : mixed {
        if(empty($key)) {
            return $this->get;
        }
        if(empty($value)) {
            return $this->get->{$key} ?? object();
        } else {
            $this->get->{$key} = $value;
        }

        return $this->get;
    }

    public function params(string $key = '', $value = '') : mixed {
        if(empty($key)) {
            return $this->params;
        }
        if(empty($value)) {
            return $this->params->{$key} ?? object();
        } else {
            $this->params->{$key} = $value;
        }

        return $this->params;
    }


    public function post(string $key = '', $value = '') : mixed {
        if(empty($key)) {
            return $this->post;
        }
        if(empty($value)) {
            return $this->post->{$key} ?? null;
        } else {
            $this->post->{$key} = $value;
        }

        return $this->post;
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
            'headers' => self::$instance->headers
        ];   
    }
}