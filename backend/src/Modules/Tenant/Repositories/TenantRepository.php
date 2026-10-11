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
        $select = 'SELECT t.uuid, t.id, b.fantasy_name, b.trade_name, b.type, b.tax_id, b.website, t.domain, t.slug, 
                    b.state_registration, b.municipal_registration, t.created_at, t.active, t.subscription_type_id, t.subscription_status_id,
                    bb.app_name, bb.logo, 
                    (select st.label from subscription_types st where st.id = t.subscription_type_id) subscription_type,
                    (select ss.label from subscription_statuses ss where ss.id = t.subscription_status_id) subscription_status';
        $from = ' FROM tenants t 
                    INNER JOIN business b on b.tenant_id = t.id 
                    INNER JOIN business_brandings bb on bb.business_id = b.id';
         
        $params = [];

        $where = ' WHERE 1 = 1';

        if (isset($parameters->search) && $parameters->search !== '') {
            $searchTerm = '%' . $parameters->search . '%';
            $columns = ['b.fantasy_name', 'b.trade_name', 'b.website', 'b.tax_id', 't.slug', 'b.state_registration', 'b.municipal_registration'];
            $conditions = array_map(fn($col) => "public.unaccent(lower({$col})) LIKE public.unaccent(lower(?))", $columns);

            $where .= ' AND (' . implode(' OR ', $conditions) . ')';
            $params = [...$params, ...array_fill(0, count($columns), $searchTerm)];
        }

        if(isset($parameters->active) && $parameters->active !== '') {
            $where .= ' AND t.active = ?';
            $params[] = $parameters->active;
        }

        if(isset($parameters->created_at) && $parameters->created_at !== '') {
            $where .= ' AND t.created_at >= ? AND t.created_at <= ?';
            $params[] = $parameters->created_at;
        }

        if(isset($parameters->subscription_type_id) && $parameters->subscription_type_id !== '') {
            $where .= ' AND t.subscription_type_id = ?';
            $params[] = $parameters->subscription_type_id;
        }

        if(isset($parameters->subscription_status_id) && $parameters->subscription_status_id !== '') {
            $where .= ' AND t.subscription_status_id = ?';
            $params[] = $parameters->subscription_status_id;
        }

        $order = ' ORDER BY t.created_at ASC';

        $response = $this->scopedQuery(
            $select . $from . $where . $order,
            $params,
            true,
            $parameters?->per_page ?? 15,
            $parameters?->page ?? 1,
            'SELECT COUNT(*)' . $from . $where
        );

        return [
            'list' => Hydrator::hydrateMany(TenantListResponse::class, $response['data']),
            'meta' => $response['meta'],
        ];
    }
}