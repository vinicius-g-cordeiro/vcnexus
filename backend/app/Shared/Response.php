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

abstract class Response{
    public static function json(?string $message = '', bool $status = true, int $code = 200, object|array $data = [], array $headers = [], bool $bShouldExit = true) : void {
        header('Content-Type: application/json');
        http_response_code($code);

        foreach ($headers as $key => $value) {
            header($key . ': ' . $value);
        }

        if(is_array($data)){
            $data = array_map('json_encode', $data);
        } else {
            $data = json_encode($data);
        }

        echo json_encode(object(message : $message, status : $status, code : $code, data : $data ));

        if($bShouldExit) exit(0);
    }

    public static function file(string $file) : void {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit(0);
    }

    public static function redirect(string $url, array $headers = []) : void {
        header('Location: ' . $url);

        foreach ($headers as $key => $value) {
            header($key . ': ' . $value);
        }
        
        exit(0);
    }


    public static function log(?string $file = 'errors', ?string $message = '', ?int $status = 500, ?bool $success = false, ?object $data = null) : void {
        header('Content-Type: text/plain');
        header('Access-Control-Allow-Origin: http://localhost');
        header('Accept: text/plain');
        http_response_code($status);
        file_put_contents('../logs/'.$file.'.log', mb_strtoupper($file) . ' - ' . date('Y-m-d H:i:s') . ' - ' . $message . "\r\n", FILE_APPEND);
    }

    function withHeader(string $key, string $value) : void {
        header($key . ': ' . $value);
    }
}