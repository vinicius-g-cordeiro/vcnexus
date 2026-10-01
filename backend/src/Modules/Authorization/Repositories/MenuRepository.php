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
use App\Modules\Authorization\DTOs\MenusResponseContext;
use App\Shared\Domain\BaseRepository;
use App\Shared\Http\Request;
use App\Modules\Authorization\Models\Menu;

final class MenuRepository extends BaseRepository
{
    public function __construct( \ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private Request $request) {
        parent::__construct($db, $tenant_id, $user_id, $roles);
    }

    protected function table(): string
    {
        return 'menus';
    }

    /**
     * Function to get the menus of the system to be shown on the sidebar
     * 
     * @return array<Menu>
     */
    public function index() : ?array {
        $result = $this->db->GetAll('SELECT
    p.id,
    p.uuid,
    p.label,
    p.icon,
    p.route,
    p."order",
    p.permissions,
    COALESCE((
        SELECT JSONB_AGG(
            JSONB_BUILD_OBJECT(
                \'id\', c.id,
                \'uuid\', c.uuid,
                \'label\', c.label,
                \'route\', c.route,
                \'icon\', c.icon,
                \'permissions\', c.permissions,
                \'order\', c."order",
                \'tenant_id\', c.tenant_id
            ) ORDER BY c."order", c.id
        )
        FROM menus c
        WHERE c.parent_id = p.id
          AND c.active = 1
    ), \'[]\'::jsonb) AS children
FROM menus p
WHERE p.parent_id IS NULL
  AND p.active = 1
ORDER BY p."order", p.id;');

        if (empty($result) || $result === false) {
            return [];
        }

        // Group the menus by parent_id and the parent with the children
        $menus = [];

        foreach ($result  as $key => $row) {
            $result[$key]['children'] = json_decode($row['children'], true);
        }

        $result = Hydrator::hydrateMany(MenusResponseContext::class, $result);
        return $result;
    }

    private function buildTree(array $byParent, int $parentId): array
{
    $tree = [];

    foreach ($byParent[$parentId] ?? [] as $row) {
        $row['children'] = $this->buildTree($byParent, (int) $row['id']);

        if ($row['route'] === null && empty($row['children'])) {
            continue;
        }

        $tree[] = $row;
    }

    return $tree;
}

}
