<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Schemas\Authentication;

use App\Schemas\AbstractSchema;
use App\Shared\Schema\Attributes\{Auditable, Column, Comment, Nullable, Timestamps, Unique};

#[Unique(name: 'uq_users_credentials_email', columns: ['email'])]
#[Timestamps(created_at: 'created_at', updated_at: 'updated_at', deleted_at: 'deleted_at')]
#[Auditable(created_by: 'created_by', updated_by: 'updated_by', deleted_by: 'deleted_by', deleted_reason: 'deleted_reason')]
final class UserCredentialsSchema extends AbstractSchema
{
    public string $table = 'user_credentials';

    #[Column(type: 'varchar', length: 60)]
    public readonly ?string $email;

    #[Column(type: 'varchar', length: 128)]
    public readonly string $password;

    #[Column(type: 'smallint', length: 1, default: null)]
    #[Nullable(nullable: true)]
    public readonly ?int $blocked;

    #[Column(type: 'smallint', length: 1,  default: null)]
    #[Comment("Users Remember for login (1 = Remember), so the user dont need to login again ")]
    #[Nullable(nullable: true)]
    public readonly ?int $remember;

    #[Column(type: 'varchar', length: 255, default: null)]
    #[Nullable()]
    public readonly ?string $token;

    #[Column(type: 'timestamp', default: null)]
    #[Nullable()]
    public readonly ?string $token_expires_at;

    #[Column(type: 'timestamp', default: null)]
    #[Nullable()]
    public readonly ?string $token_created_at;

    #[Column(type: 'timestamp', default: null)]
    #[Nullable()]
    public readonly ?string $last_login_at;

    #[Column(type: 'timestamp', default: null)]
    #[Nullable()]
    public readonly ?string $last_login_at_local;

    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable()]
    public readonly ?string $last_login_ip;

    #[Column(type: 'varchar', length: 100, default: null)]
    #[Nullable()]
    public readonly ?string $last_login_agent;

}