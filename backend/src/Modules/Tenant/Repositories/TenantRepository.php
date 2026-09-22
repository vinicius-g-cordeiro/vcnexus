<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Tenant\Repositories;

use App\Shared\Domain\BaseRepository;
use App\Infrastructure\Database\Hydrator;
use App\Modules\Tenant\DTOs\{TenantListRequest,TenantListResponse};
use App\Shared\Http\Request;

final class TenantRepository extends BaseRepository
{

    public function __construct( \ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private Request $request) {
        parent::__construct($db, $tenant_id, $user_id, $roles);
    }

    protected function table(): string { return 'tenants'; }

    public function findIdBySlugUnscoped(string $slug): ?string
    {
        return $this->db->GetOne("SELECT id FROM {$this->table()} WHERE slug = ?", [$slug]);
    }

    /**
     * @throws \Exception
     * @param mixed $parameters
     * @return TenantListResponse[]|null
     */
    public function list(?TenantListRequest $parameters = null): ?array
    {
         $sql = 'SELECT t.uuid, t.id, b.fantasy_name, b.trade_name, b.type, b.tax_id, b.website, t.domain, t.slug, b.state_registration, b.municipal_registration,
                    t.created_at, t.active, t.subscription_type, t.subscription_status, bb.app_name, bb.logo
                    FROM tenants t 
                    INNER JOIN business b on b.tenant_id = t.id 
                    INNER JOIN business_brandings bb on bb.business_id = b.id';

        $params = [];

        if (isset($parameters->search) && $parameters->search !== '') {
            $searchTerm = '%' . $parameters->search . '%';
            $columns = ['b.fantasy_name', 'b.trade_name', 'b.website', 'b.tax_id', 't.slug', 't.domain', 'b.state_registration', 'b.municipal_registration'];
            $conditions = array_map(fn($col) => "public.unaccent(lower({$col})) LIKE public.unaccent(lower(?))", $columns);

            $sql .= ' AND (' . implode(' OR ', $conditions) . ')';
            $params = [...$params, ...array_fill(0, count($columns), $searchTerm)];
        }

        if(isset($parameters->active) && $parameters->active !== '') {
            $sql .= ' AND t.active = ?';
            $params[] = $parameters->active;
        }

        if(isset($parameters->created_at) && $parameters->created_at !== '') {
            $sql .= ' AND t.created_at >= ? AND t.created_at <= ?';
            $params[] = $parameters->created_at;
        }

        $result = $this->scopedQuery($sql, $params);
        
        return !empty($result) && $result !== false ? Hydrator::hydrateMany(TenantListResponse::class, $result) : null;
    }
}