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
use App\Modules\Authorization\DTOs\PermissionResponseContext;
use App\Shared\Domain\BaseRepository;
use App\Shared\Http\Request;

final class PermissionRepository extends BaseRepository
{
    public function __construct( \ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private Request $request) {
        parent::__construct($db, $tenant_id, $user_id, $roles);
    }

    protected function table(): string
    {
        return 'permissions';
    }

    /**
     * Function to get the permissions of the system to be shown on the sidebar
     * 
     * @return array<PermissionResponseContext>
     */
    public function index() : ?array {
        $query = <<<SQL
            SELECT p.id, p.uuid, p.name, p.slug, p.description, p.tenant_id, p.active, p.created_at
            FROM permissions p
        SQL;

        $where = 'p.tenant_id = ?'; 

        $query .= " WHERE $where";

        $params = [$this->tenant_id];

        $result = $this->scopedQuery($query, $params);

        if (empty($result) || $result === false) {
            return [];
        }

        $result = Hydrator::hydrateMany(PermissionResponseContext::class, $result);
        return $result;
    }

    
}
