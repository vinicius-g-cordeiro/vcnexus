<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Users\Repositories;

use App\Shared\Domain\BaseRepository;
use App\Modules\Users\DTOs\UserTenantMembershipResponse;
use App\Infrastructure\Database\Hydrator;

final class UserTenantMembershipRepository extends BaseRepository
{

    protected function table(): string
    {
        return 'tenant_memberships';
    }


    public function findByIdUnscoped(string $user_id): ?UserTenantMembershipResponse
    {
        $result = $this->db->GetRow("select * from public.get_tenant_memberships_access(?)", [(int)$user_id]);
        return $result ? Hydrator::hydrate(UserTenantMembershipResponse::class, $result) : null;
    }
}