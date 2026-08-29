<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';


header('Content-Type: application/json');
http_response_code(200);
echo json_encode([
    'status' => 'ok',
    'success' => true,
    'code' => 200,
    'data' => []
]);

exit(0);