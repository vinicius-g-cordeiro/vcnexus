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

use App\Modules\Users\DTOs\UserListRequest;
use App\Modules\Users\DTOs\UsersListResponse;
use App\Shared\Domain\BaseRepository;
use App\Infrastructure\Database\Hydrator;
use App\Shared\Http\Request;


final class UserProfileRepository extends BaseRepository
{
    public function __construct( \ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private Request $request) {
        parent::__construct($db, $tenant_id, $user_id, $roles);
    }

    protected function table(): string
    {
        return 'user_profile';
    }


    /**
     * List all users
     * @param UserListRequest|null $parameters
     * @return array<UsersListResponse>|bool
     * @throws \Exception
     */
    public function list(?UserListRequest $parameters): ?array {

        $sql = 'SELECT uc.email, up.*,uc.uuid, uc.id, uc.created_at, (select array_agg(upe.slug) 
            from user_permissions up2 
            inner join permissions upe on up2.permission_id = upe.id
            where up2.user_id = uc.id) "permissions",
            (select array_agg(r.name) from user_roles ur inner join roles r on ur.role_id = r.id where ur.user_id = uc.id) "roles",
            (select array_agg(uc2.value) from user_contact uc2 where uc2.user_id = uc.id and uc2.type = 1) "emails",
            (select array_agg(uc2.value) from user_contact uc2 where uc2.user_id = uc.id and uc2.type = 2) "phones",
            (select array_agg(ua.address) from user_address ua where ua.user_id = uc.id) "addresses",
            b.fantasy_name as organization_name
            from user_credentials uc
            inner join user_profile up on uc.id = up.user_id
            inner join tenant_memberships tm on tm.user_id  = uc.id
            inner join tenants t on t.id = tm.tenant_id
            inner join business b on b.tenant_id = t.id 
        ';

        $params = [];

        if (isset($parameters->search) && $parameters->search !== '') {
            $searchTerm = '%' . $parameters->search . '%';
            $columns = ['up.firstname', 'up.surname', 'up.lastname', 'uc.email'];
            $conditions = array_map(fn($col) => "public.unaccent(lower({$col})) LIKE public.unaccent(lower(?))", $columns);

            $sql .= ' AND (' . implode(' OR ', $conditions) . ')';
            $params = [...$params, ...array_fill(0, count($columns), $searchTerm)];
        }

        if(isset($parameters->active) && $parameters->active !== '') {
            $sql .= ' AND uc.active = ?';
            $params[] = $parameters->active;
        }

        if(isset($parameters->blocked) && $parameters->blocked !== '') {
            $sql .= ' AND uc.blocked = ?';
            $params[] = $parameters->blocked;
        }

        if(isset($parameters->created_at) && $parameters->created_at !== '') {
            $sql .= ' AND uc.created_at >= ? AND uc.created_at <= ?';
            $params[] = $parameters->created_at;
        }

        $result = $this->scopedQuery($sql, $params);
        
        return !empty($result) && $result !== false ? Hydrator::hydrateMany(UsersListResponse::class, $result) : null;
    }

    
}