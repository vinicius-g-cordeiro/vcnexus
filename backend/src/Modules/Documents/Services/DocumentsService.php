<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Documents\Services;

use ADOConnection;
use App\Shared\Domain\BaseService;
use App\Modules\Documents\DTOs\DocumentListRequest;
use App\Modules\Documents\Repositories\DocumentsRepository;

final class DocumentsService extends BaseService
{
    public function __construct(ADOConnection $db, ?string $tenant_id, ?string $user_id, ?array $roles, private DocumentsRepository $documentsRepository) {
        parent::__construct($db, $tenant_id, $user_id, $roles);
    }

    public function list(?DocumentListRequest $parameters = null): ?array {
        return $this->documentsRepository->list($parameters);
    }

}
