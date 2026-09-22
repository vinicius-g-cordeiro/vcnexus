<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$redis = new Redis();
$redis->connect('redis', 6379);
$redis->auth(trim(file_get_contents(getenv('REDIS_PASSWORD'))));

$stream = 'chat:messages';
$group = 'chat-consumers';

try {
    $redis->xGroup('CREATE', $stream, $group, '0', true);
} catch (\RedisException $e) {
    // group already exists — fine
}

while (true) {
    $messages = $redis->xReadGroup($group, 'worker-1', [$stream => '>'], 10, 2000);

    if ($messages === false || empty($messages)) {
        continue;
    }

    foreach ($messages[$stream] as $id => $fields) {
        try {
            
            /// @todo Add message to the database here
            $redis->xAck($stream, $group, [$id]);
        } catch (\Throwable $e) {
            error_log("Failed to process message {$id}: {$e->getMessage()}");
            // leave unpacked — will be redelivered / can build a dead-letter path later
        }
    }
}