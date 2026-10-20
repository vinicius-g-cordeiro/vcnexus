<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\Http;

final class Response {

    private static ?Response $instance = null;

    private static ?string $json = null;

    private static ?string $xml = null;

    public static function json(object|array|null $data = null, string $message = ''): ?Response {
        self::$json = json_encode(object(message: $message, data: $data));
        return Response::getInstance();
    }

    public static function xml(object|array|null $data, string $message = ''): ?Response {
        $xmlDocument = new \DOMDocument('1.0', 'utf-8');
        $xmlDocument->formatOutput = true;
        $xmlDocument->preserveWhiteSpace = false;
        $xmlDocument->loadXML(json_encode(object(message: $message, data: $data)));
        self::$xml =$xmlDocument->saveXML();
        return Response::getInstance();
    }

    public static function log(?string $file = 'error', ?string $message = '', ?int $status = 500, ?bool $success = false, ?object $data = null) : void {
        @file_put_contents(__DIR__ . '/../../../storage/logs/'.$file.'.log', mb_strtoupper($file) . ' - ' . date('Y-m-d H:i:s') . ' - IP: ' . ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0')) . ' - ' . $message . "\r\n", FILE_APPEND);   
    }

    public static function send(int $code = 200, array $headers = [], ?bool $bExit = true): ?Response {
        http_response_code($code);
        header('HTTP/1.1 ' . $code );
        if (self::$json !== null) {
            header('Content-Type: application/json');
            echo self::$json;
        } elseif (self::$xml !== null) {
            header('Content-Type: application/xml');
            echo self::$xml;
        }

        foreach ($headers as $header => $value) {
            header($header . ': ' . $value);
        }


        if ($bExit) {
            exit;
        }

        return Response::getInstance();
    }

    private static function getInstance(): Response {
        if(!isset(self::$instance)) {
            self::$instance = new Response();
        }
        return self::$instance;
    }

    public static function getJson(): ?string {
        return self::$json;
    }
}