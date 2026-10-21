<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Documents\Repositories;

use ADOConnection;
use App\Shared\Http\Request;
use App\Shared\Domain\BaseRepository;
use App\Infrastructure\Database\Hydrator;
use App\Modules\Documents\Models\Document;
use App\Modules\Documents\DTOs\DocumentListRequest;

final class DocumentsRepository extends BaseRepository
{

    public function __construct( ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private Request $request) {
        parent::__construct($db, $tenant_id, $user_id, $roles);
    }

    protected function table(): string { return 'documents'; }

    /**
     * @throws \Exception
     * @param mixed $parameters
     * @return []|null
     */
    public function list(?DocumentListRequest $parameters = null): ?array
    {
        $select = 'SELECT d.*';
        $from = ' FROM documents d';

        $params = [];

        $where = ' WHERE 1 = 1';

        if (isset($parameters->search) && $parameters->search !== '') {
            $searchTerm = '%' . $parameters->search . '%';
            $columns = ['d.name', 'd.tags', 'd.slug', 'd.id'];
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

        $order = ' ORDER BY t.created_at ASC';

        $response = $this->scopedQuery(
            $select . $from . $where . $order,
            $params,
            $parameters?->paginate ?? true,
            $parameters?->per_page ?? 15,
            $parameters?->page ?? 1,
            'SELECT COUNT(*)' . $from . $where
        );

        return [
            'list' => Hydrator::hydrateMany(Document::class, $response['data']??$response??[]),
            'meta' => $response['meta']??[],
        ];
    }

}
