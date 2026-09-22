<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Authentication\Repositories;

use App\Infrastructure\Database\Hydrator;
use App\Modules\Authentication\DTOs\CredentialsResponse;
use App\Shared\Domain\BaseRepository;
use App\Modules\Authentication\DTOs\LoginResponse;

final class AuthenticationRepository extends BaseRepository
{

    protected function table(): string
    {
        return 'user_credentials';
    }

    public function findUserAccountUnscoped(string $login): ?CredentialsResponse
    {
        $query =    "SELECT u.blocked, u.id, u.uuid, u.active, u.password
        FROM {$this->table()} u WHERE (public.unaccent(lower(u.email)) = public.unaccent(lower(?))) LIMIT 1";

        $result = $this->db->GetRow($query, [$login]);
        
        
        return empty($result) === false ? Hydrator::hydrate(CredentialsResponse::class, $result) : null;
    }
    

    public function getUserTenants(string $user_id): ?LoginResponse
    {
        $res = $this->db->Execute("select * from public.get_user_tenants(?)", [$user_id]);
        $result = $this->fr2Arr($res)[0] ?? false;
        
        return !empty($result) ? Hydrator::hydrate(LoginResponse::class,$result) : null;
    }
}