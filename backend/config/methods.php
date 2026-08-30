<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace Config;

enum Methods : string {
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case PATCH = 'PATCH';
    case QUERY = 'QUERY';
}

return [
    'methods' => [
        'get' => Methods::GET,
        'post' => Methods::POST,
        'put' => Methods::PUT,
        'delete' => Methods::DELETE,
        'patch' => Methods::PATCH,
        'query' => Methods::QUERY
    ]
];