<?php
/**
 * @brief Verifies valid insert and update field mapping without requiring a live database.
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0
 * @date 2026/09/12
 */

declare(strict_types=1);

namespace Tests;

use ADOConnection;
use App\Database\Schema\UsersSchema;
use App\DTOs\Users\AvatarStoreDTO;
use App\DTOs\Users\UserRegistrationDTO;
use App\Model\Model;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

require_once __DIR__ . '/../vendor/autoload.php';

final class PersistenceContractTest extends TestCase
{
    /** @brief Verifies that a valid user insert maps DTO fields to the users table. */
    public function testValidInsert(): void
    {
        $connection = $this->createMock(ADOConnection::class);
        $connection->expects(self::once())->method('AutoExecute')->with(
            'users',
            self::callback(static fn (array $fields): bool =>
                $fields['name'] === 'Ada'
                && $fields['email'] === 'ada@example.test'
                && isset($fields['password'], $fields['created_at'])
            ),
            'INSERT'
        )->willReturn(true);
        $connection->method('Insert_ID')->willReturn(5);
        $connection->method('GetRow')->willReturn((object) ['tenant_id' => 10, 'uuid' => 'user-uuid']);

        $model = $this->modelWithoutConstructor($connection);
        $result = $model->store(new UserRegistrationDTO(
            name: 'Ada',
            surname: '',
            lastname: 'Lovelace',
            username: 'ada.lovelace',
            email: 'ada@example.test',
            password: 'A secure password 123!',
            password_confirmation: 'A secure password 123!',
            tenant_id: 10
        ));

        self::assertSame(5, $result->insertID);
        self::assertSame('user-uuid', $result->uuid);
    }

    /** @brief Verifies that a valid profile update maps only editable fields. */
    public function testValidUpdate(): void
    {
        $connection = $this->createMock(ADOConnection::class);
        $connection->expects(self::once())->method('AutoExecute')->with(
            'users',
            self::callback(static fn (array $fields): bool =>
                $fields['avatar'] === '/storage/avatar.png'
                && !isset($fields['uuid'])
            ),
            'UPDATE',
            "uuid = 'user-uuid'"
        )->willReturn(true);
        $connection->method('GetRow')->willReturn((object) [
            'id' => 5,
            'tenant_id' => 10,
            'uuid' => 'user-uuid',
        ]);

        $model = $this->modelWithoutConstructor($connection);
        $result = $model->update(
            new AvatarStoreDTO('user-uuid', '/storage/avatar.png'),
            "uuid = 'user-uuid'"
        );

        self::assertSame('user-uuid', $result->uuid);
    }

    /** @brief Creates a model with only the dependencies needed by the field mapping methods. */
    private function modelWithoutConstructor(ADOConnection $connection): Model
    {
        $model = (new \ReflectionClass(Model::class))->newInstanceWithoutConstructor();
        $schema = new UsersSchema();

        $schemaProperty = new ReflectionProperty(Model::class, 'schema');
        $schemaProperty->setValue($model, $schema);

        $connectionProperty = new ReflectionProperty(\App\Shared\Connection::class, 'connection');
        $connectionProperty->setValue($model, $connection);

        return $model;
    }
}
