<?php
/**
 * @brief Verifies avatar DTO data and the upload directory contract.
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0
 * @date 2026/09/12
 */

declare(strict_types=1);

namespace Tests;

use App\DTOs\Users\AvatarStoreDTO;
use App\Shared\Helpers\Files;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

final class AvatarUploadTest extends TestCase
{
    /** @brief Verifies that avatar metadata points to a supported image file. */
    public function testValidAvatarUploadMetadata(): void
    {
        $avatar = new AvatarStoreDTO('user-uuid', '/storage/upload/users/avatars/avatar.png');

        self::assertSame('user-uuid', $avatar->uuid);
        self::assertSame('png', strtolower(pathinfo($avatar->avatar, PATHINFO_EXTENSION)));
    }

    /** @brief Verifies that the avatar upload directory can be created by the file helper. */
    public function testAvatarUploadDirectory(): void
    {
        $directory = sys_get_temp_dir() . '/vcnexus-avatar-' . bin2hex(random_bytes(6));
        $files = new Files();

        try {
            self::assertTrue($files->createFolder($directory));
            self::assertDirectoryExists($directory);
        } finally {
            if (is_dir($directory)) {
                rmdir($directory);
            }
        }
    }
}
