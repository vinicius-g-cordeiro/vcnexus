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

use App\Shared\Notification;

final class Response{

    public static function json(?string $message = '', bool $status = true, int $code = 200, object|array $data = [], array $headers = [], bool $bShouldExit = true, string $redirectUrl = '') {
        header('Content-Type: application/json');
        http_response_code($code);

        foreach ($headers as $key => $value) {
            header($key . ': ' . $value);
        }

        echo json_encode(object(message : $message, status : $status, code : $code, data : $data ), JSON_UNESCAPED_UNICODE);

        if($bShouldExit) exit(0);
    }

    public static function file(string $file) : void {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit(0);
    }

    public static function redirect(string $url, array $headers = [], bool $showNotification = false, ?Notification $notification = null) : void {
      
        foreach ($headers as $key => $value) {
            header($key . ': ' . $value);
        }

        // If we should show notification as soon as we redirect, we are showing adding the notification to the session 
        if($showNotification == true){
            if($notification === null) return;
            Session::getInstance()->addNotification($notification);
        }
        
        if(headers_sent())
        {
            $string = '<script type="text/javascript">';
            $string .= 'window.location = "' . $url . '"';
            $string .= '</script>';
            echo $string;
        }
        else
        {
            http_response_code(302);
            header('Location: '.$url);
        }
        exit(0);
    }

    public static function notification(?Notification $notification) : void {
        if($notification === null) return;
        Session::getInstance()->addNotification($notification);
        
    }


    public static function log(?string $file = 'error', ?string $message = '', ?int $status = 500, ?bool $success = false, ?object $data = null) : void {
        header('Content-Type: text/plain');
        header('Accept: text/plain');
        http_response_code($status);
        file_put_contents(__DIR__ . '/../../storage/logs/'.$file.'.log', mb_strtoupper($file) . ' - ' . date('Y-m-d H:i:s') . ' - ' . $message . "\r\n", FILE_APPEND);
    }

    function withHeader(string $key, string $value) : void {
        header($key . ': ' . $value);
    }
}