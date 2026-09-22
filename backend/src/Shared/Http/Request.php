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

final class Request
{
    private array $attributes = [];

    public ?object $params;
    public ?object $get;
    public ?object $post;
    public ?object $files;
    public ?object $patch;
    public ?object $put;
    public ?object $delete;

    public string $uri;
    public string $path;
    public string $method;
    public array $routeParams = [];
    public array $headers = [];

    public function __construct()
    {
        $this->params = (object) filter_var_array($_REQUEST, FILTER_SANITIZE_SPECIAL_CHARS);
        $this->get = (object) filter_var_array($_GET, FILTER_SANITIZE_SPECIAL_CHARS);
        $this->post = (object) filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        $this->files = (object) filter_var_array($_FILES, FILTER_SANITIZE_SPECIAL_CHARS);
        $this->patch = null;
        $this->put = null;
        $this->delete = null;

        $this->uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $this->method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $this->path = '/' . trim(
            (string) (parse_url($this->uri, PHP_URL_PATH) ?: '/'),
            '/'
        );

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $this->headers[str_replace('_', '-', substr($key, 5))] = (string) $value;
            }
        }

        // Get the headers from the $_SERVER starting with HTTP_
        // $this->headers = array_filter($_SERVER, fn ($key) => str_starts_with($key, 'HTTP_'));
        $this->parseRequestBody();
    }

    private function parseRequestBody(): void
    {
        $input = file_get_contents('php://input');
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        $data = $this->decodeBody($input, $contentType);
        match ($this->method) {
            'POST' => $this->post = (object) $data,
            'PUT' => $this->put = (object) $data,
            'PATCH' => $this->patch = (object) $data,
            'DELETE' => $this->delete = (object) $data,
            default => null, // GET and others carry no body — $get is already populated from $_GET
        };
    }

    private function decodeBody(string $input, string $contentType): array
    {
        if ($input === '') {
            return [];
        }

        if (str_contains($contentType, 'application/json')) {
            $decoded = json_decode($input, true);
            return is_array($decoded)
                ? filter_var_array($decoded, FILTER_SANITIZE_SPECIAL_CHARS) ?: []
                : [];
        }

        parse_str($input, $data);
        return $data;
    }


    public function withAttribute(string $key, mixed $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function attribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    public function get(string $key = '', mixed $value = ''): mixed
    {
        if ($key === '') {
            return $this->get;
        }
        if ($value === '') {
            return $this->get->{$key} ?? null;
        }
        $this->get->{$key} = $value;
        return $this->get;
    }

    public function post(string $key = '', mixed $value = ''): mixed
    {
        if ($key === '') {
            return $this->post;
        }
        if ($value === '') {
            return $this->post->{$key} ?? null;
        }
        $this->post->{$key} = $value;
        return $this->post;
    }

    public function put(string $key = '', mixed $value = ''): mixed
    {
        if ($key === '') {
            return $this->put;
        }
        if ($value === '') {
            return $this->put?->{$key} ?? null;
        }
        $this->put ??= (object) [];
        $this->put->{$key} = $value;
        return $this->put;
    }

    public function patch(string $key = '', mixed $value = ''): mixed
    {
        if ($key === '') {
            return $this->patch;
        }
        if ($value === '') {
            return $this->patch?->{$key} ?? null;
        }
        $this->patch ??= (object) [];
        $this->patch->{$key} = $value;
        return $this->patch;
    }

    public function delete(string $key = '', mixed $value = ''): mixed
    {
        if ($key === '') {
            return $this->delete;
        }
        if ($value === '') {
            return $this->delete?->{$key} ?? null;
        }
        $this->delete ??= (object) [];
        $this->delete->{$key} = $value;
        return $this->delete;
    }

    public function params(string $key = '', mixed $value = ''): mixed
    {
        if ($key === '') {
            return $this->params;
        }
        if ($value === '') {
            return $this->params->{$key} ?? null;
        }
        $this->params->{$key} = $value;
        return $this->params;
    }

    public function files(string $key = '', mixed $value = ''): mixed
    {
        if ($key === '') {
            return $this->files;
        }
        if ($value === '') {
            return $this->files->{$key} ?? null;
        }
        $this->files->{$key} = $value;
        return $this->files;
    }

    public function ip(): string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public function headers(string $key = '', mixed $value = ''): mixed
    {
        if ($key === '') {
            return $this->headers;
        }
        if ($value === '') {
            return $this->headers[$key] ?? null;
        }
        $this->headers[$key] = $value;
        return $this->headers;
    }

    public function all(): array
    {
        return [
            'params' => $this->params,
            'get' => $this->get,
            'post' => $this->post,
            'put' => $this->put,
            'patch' => $this->patch,
            'delete' => $this->delete,
            'files' => $this->files,
            'uri' => $this->uri,
            'path' => $this->path,
            'method' => $this->method,
            'headers' => $this->headers,
            'attributes' => $this->attributes,
        ];
    }
}