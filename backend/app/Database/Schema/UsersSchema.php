<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Database\Schema;

use App\Database\Attributes\UniqueConstraint;
use App\Database\Schema\Schema;
use App\Database\Attributes\Column;

#[UniqueConstraint(name: 'uq_users_email', columns: ['email'])]
#[UniqueConstraint(name: 'uq_users_username', columns: ['username'])]
#[UniqueConstraint(name: 'uq_users_phone', columns: ['phone'])]
class UsersSchema extends Schema
{
    public string $table = 'users';

    #[Column(type: 'VARCHAR(100)', default: '', nullable: false)]
    public string $name = '';

    #[Column(type: 'VARCHAR(64)', default: '', nullable: false)]
    public string $password = '';

    #[Column(type: 'VARCHAR(100)', default: '', nullable: true, comment: '')]
    public ?string $email = null;

    #[Column(type: 'VARCHAR(24)', default: null, nullable: true, comment: '')]
    public ?string $phone = null;

    #[Column(type: 'VARCHAR(100)', default: null, nullable: true, comment: '')]
    public ?string $username = '';

    #[Column(type: 'SMALLINT', default: null, nullable: true, comment: '')]
    public ?int $blocked = null;

    #[Column(type: 'BIGINT', default: null, nullable: true, comment: '')]
    public ?string $blocked_by = null;

    #[Column(type: 'TIMESTAMP', default: null, nullable: true, comment: '')]
    public ?string $blocked_at = null;

    #[Column(type: 'TIMESTAMP', default: null, nullable: true, comment: 'Last time the user was logged in on the system')]
    public ?string $last_login = null;

    #[Column(type: 'TIMESTAMP WITH TIME ZONE', default: null, nullable: true, comment: 'Last time the user was logged in on the system')]
    public ?string $last_login_local = null;

    #[Column(type: 'VARCHAR(50)', default: null, nullable: true, comment: '')]
    public ?string $last_ip = null;

    #[Column(type: 'VARCHAR(500)', default: null, nullable: true, comment: '')]
    public ?string $last_agent = null;

    #[Column(type: 'BIGINT', default: null, nullable: true, inherit: false)]
    public ?int $tenant_id = null;


    function __construct() {
        parent::__construct();
    }
}