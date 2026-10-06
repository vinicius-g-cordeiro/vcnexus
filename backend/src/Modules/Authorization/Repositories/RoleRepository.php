<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Authorization\Repositories;

use App\Infrastructure\Database\Hydrator;
use App\Modules\Authorization\DTOs\RoleResponseContext;
use App\Shared\Domain\BaseRepository;
use App\Shared\Http\Request;

final class RoleRepository extends BaseRepository
{
    public function __construct( \ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private Request $request) {
        parent::__construct($db, $tenant_id, $user_id, $roles);
    }

    protected function table(): string
    {
        return 'roles';
    }

    /**
     * Function to get the roles of the system to be shown on the sidebar
     * 
     * @return array<RoleResponseContext>
     */
    public function index() : ?array {
        $query = <<<SQL
            SELECT r.id, r.uuid, r.name, r.description, r.tenant_id, r.active, r.created_at, b.fantasy_name as organization_name
            FROM roles r
            LEFT JOIN business b on b.tenant_id = r.tenant_id
            WHERE 1 = 1
        SQL;

        $params = [];

        // Verify if we are not using the super admin user, if not we will filter by tenant
        if(in_array("1", $this->roles) === false) {
            $query .= ' AND r.tenant_id = ?';
            $params[] = $this->tenant_id;
        }

        $query .= ' ORDER BY r.id ASC';

        $result = $this->scopedQuery($query, $params);

        if (empty($result) || $result === false) {
            return [];
        }

        $result = Hydrator::hydrateMany(RoleResponseContext::class, $result);
        return $result;
    }

    
}
