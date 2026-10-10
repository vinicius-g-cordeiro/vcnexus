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

use App\Infrastructure\Redis\RedisConnectionFactory;
use App\Modules\Users\DTOs\UserCredentialsResponse;
use App\Modules\Users\DTOs\UserListRequest;
use App\Modules\Users\DTOs\UsersListResponse;
use App\Modules\Users\Models\{UserProfile, UserAddress, UserConsents, UserContact, UserEducation, UserSensitive};
use App\Shared\Domain\BaseRepository;
use App\Infrastructure\Database\Hydrator;
use App\Shared\Http\Request;


final class UserProfileRepository extends BaseRepository
{

    private \Redis $redis;
    public function __construct( \ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private Request $request, private RedisConnectionFactory $redisConnectionFactory) {
        parent::__construct($db, $tenant_id, $user_id, $roles);

        $this->redis = $this->redisConnectionFactory->create();

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

        $select = 'SELECT uc.email, up.*,uc.uuid, uc.id, uc.created_at, uc.active, uc.blocked, uc.status as online_status, (select array_agg(upe.slug)
            from user_permissions up2 
            inner join permissions upe on up2.permission_id = upe.id
            where up2.user_id = uc.id) "permissions",
            (select array_agg(r.name) from user_roles ur inner join roles r on ur.role_id = r.id where ur.user_id = uc.id) "roles",
            (select array_agg(uc2.value) from user_contact uc2 where uc2.user_id = uc.id and uc2.type = 1) "emails",
            (select array_agg(uc2.value) from user_contact uc2 where uc2.user_id = uc.id and uc2.type = 2) "phones",
            (select array_agg(ua.address) from user_address ua where ua.user_id = uc.id) "addresses",
            b.fantasy_name as organization_name
        ';

        $from = ' FROM user_credentials uc
            inner join user_profile up on uc.id = up.user_id
            inner join tenant_memberships tm on tm.user_id = uc.id
            inner join tenants t on t.id = tm.tenant_id
            inner join business b on b.tenant_id = t.id';

        $where = ' WHERE 1=1';
        $params = [];

        if (isset($parameters->search) && $parameters->search !== '') {
            $searchTerm = '%' . $parameters->search . '%';
            $columns = ['up.firstname', 'up.surname', 'up.lastname', 'uc.email', 'uc.username', 'b.fantasy_name'];
            $conditions = array_map(fn($col) => "public.unaccent(lower({$col})) LIKE public.unaccent(lower(?))", $columns);

            $where .= ' AND (' . implode(' OR ', $conditions) . ')';
            $params = [...$params, ...array_fill(0, count($columns), $searchTerm)];
        }

        if (isset($parameters->active) && $parameters->active !== '') {
            $where .= ' AND uc.active = ?';
            $params[] = (int) $parameters->active;
        }

        if (isset($parameters->blocked) && $parameters->blocked !== '' && $parameters->blocked === 1) {
            $where .= ' AND uc.blocked = ?';
            $params[] = (int) $parameters->blocked;
        }

        if (isset($parameters->created_at) && $parameters->created_at !== '') {
            $where .= ' AND uc.created_at >= ?::date AND uc.created_at < (?::date + 1)';
            $params[] = $parameters->created_at;
            $params[] = $parameters->created_at;
        }

        $order = ' ORDER BY uc.created_at DESC, uc.id DESC';

        $response = $this->scopedQuery(
            $select . $from . $where . $order,
            $params,
            true,
            $parameters?->per_page ?? 15,
            $parameters?->page ?? 1,
            'SELECT COUNT(*)' . $from . $where
        );

        return [
            'list' => Hydrator::hydrateMany(UsersListResponse::class, $response['data']),
            'meta' => $response['meta'],
        ];
    }


    public function profile(string $uuid): ?UserProfile {

        $select = <<<SQL
            SELECT up.* 
            FROM user_credentials uc
            inner join user_profile up on uc.id = up.user_id
            inner join tenant_memberships tm on tm.user_id = uc.id
            inner join tenants t on t.id = tm.tenant_id
            inner join business b on b.tenant_id = t.id
        SQL;

        $where = ' WHERE uc.uuid = ?';

        $user = $this->scopedQuery($select . $where, [$uuid], false)[0] ?? null;
    
        if (!$user) {
            return null;
        }

        $user = Hydrator::hydrate(UserProfile::class, $user);

        return $user;
    }

    public function credentials(string $uuid): ?UserCredentialsResponse {
        $select = <<<SQL
            SELECT uc.id, uc.uuid, uc.active, uc.email, uc.blocked, uc.status
            FROM user_credentials uc
            inner join user_profile up on uc.id = up.user_id
            inner join tenant_memberships tm on tm.user_id = uc.id
            inner join tenants t on t.id = tm.tenant_id
            inner join business b on b.tenant_id = t.id
        SQL;

        $where = ' WHERE uc.uuid = ?';

        $user = $this->scopedQuery($select . $where, [$uuid], false)[0] ?? null;
    
        if (!$user) {
            return null;
        }

        $user = Hydrator::hydrate(UserCredentialsResponse::class, $user);

        return $user;
    }


    /**
     * 
     * @param string $uuid
     * @return array<UserAddress>|null
     */
    public function addresses(string $uuid): ?array {
        $select = <<<SQL
            select ua.*
            FROM user_address ua 
            inner join user_credentials uc on uc.id = ua.user_id
            inner join user_profile up on uc.id = up.user_id
            inner join tenant_memberships tm on tm.user_id = uc.id
            inner join tenants t on t.id = tm.tenant_id
            inner join business b on b.tenant_id = t.id
        SQL;

        $where = ' WHERE uc.uuid = ?';

        $user = $this->scopedQuery($select . $where, [$uuid], false) ?? null;
    
        if (!$user) {
            return null;
        }

        return Hydrator::hydrateMany(UserAddress::class, $user);
    }

    /**
     * 
     * @param string $uuid
     * @return UserContact[]|null
     */
    public function contacts(string $uuid): ?array {
        $select = <<<SQL
            select ucon.*
            FROM user_contact ucon 
            inner join user_credentials uc on uc.id = ucon.user_id
            inner join user_profile up on uc.id = up.user_id
            inner join tenant_memberships tm on tm.user_id = uc.id
            inner join tenants t on t.id = tm.tenant_id
            inner join business b on b.tenant_id = t.id
        SQL;

        $where = ' WHERE uc.uuid = ?';

        $user = $this->scopedQuery($select . $where, [$uuid], false) ?? null;
    
        if (!$user) {
            return null;
        }

        return Hydrator::hydrateMany(UserContact::class, $user);
    }

    /**
     * 
     * @param string $uuid
     * @return UserConsents[]|null
     */
    public function consents(string $uuid): ?array {
        $select = <<<SQL
            select ucon.*
            FROM user_consents ucon 
            inner join user_credentials uc on uc.id = ucon.user_id
            inner join user_profile up on uc.id = up.user_id
            inner join tenant_memberships tm on tm.user_id = uc.id
            inner join tenants t on t.id = tm.tenant_id
            inner join business b on b.tenant_id = t.id
        SQL;

        $where = ' WHERE uc.uuid = ?';

        $user = $this->scopedQuery($select . $where, [$uuid], false) ?? null;
    
        if (!$user) {
            return null;
        }

        return Hydrator::hydrateMany(UserConsents::class, $user);
    }

    public function sensitive(string $uuid): ?UserSensitive {
        $select = <<<SQL
            select us.*
            FROM user_sensitive us 
            inner join user_credentials uc on uc.id = us.user_id
            inner join user_profile up on uc.id = up.user_id
            inner join tenant_memberships tm on tm.user_id = uc.id
            inner join tenants t on t.id = tm.tenant_id
            inner join business b on b.tenant_id = t.id
        SQL;

        $where = ' WHERE uc.uuid = ?';

        $user = $this->scopedQuery($select . $where, [$uuid], false)[0] ?? null;
    
        if (!$user) {
            return null;
        }

        return Hydrator::hydrate(UserSensitive::class, $user);
    }


    public function education(string $uuid): ?array {
        $select = <<<SQL
            SELECT ue.*
            FROM user_education ue 
            inner join user_credentials uc on uc.id = ue.user_id
            inner join user_profile up on uc.id = up.user_id
            inner join tenant_memberships tm on tm.user_id = uc.id
            inner join tenants t on t.id = tm.tenant_id
            inner join business b on b.tenant_id = t.id
        SQL;

        $where = ' WHERE uc.uuid = ?';

        $user = $this->scopedQuery($select . $where, [$uuid], false) ?? null;
    
        if (!$user) {
            return null;
        }

        return Hydrator::hydrateMany(UserEducation::class, $user);
    }
    

    
}