<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Authentication\DTOs;

use App\Shared\Domain\DataTransferObjectInterface;

final class LoginSuccessRequest implements DataTransferObjectInterface
{
    public function __construct(public readonly string $uuid, public readonly string $last_login_at = '', public readonly string $last_login_ip = '', public readonly string $last_login_agent = '', public readonly ?int $status){}

    public function toArray(): array{ return get_object_vars($this); }

    public static function fromArray(array $data): self{ return new self( $data['uuid'],$data['last_login_at'], $data['last_login_ip'], $data['last_login_agent'], $data['status']); }
}