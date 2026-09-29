<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);


namespace App\Shared\Domain;

use App\Infrastructure\Database\RLS\{RoleContext, TenantContext, UserContext};
use App\Shared\Domain\DTOs\{StoreResponse, UpdateResponse};
use ADORecordSet;
use App\Shared\Domain\Exceptions\TransactionFailedException;
use App\Shared\Helpers\Utils;


abstract class BaseRepository
{

    public function __construct(
        protected \ADOConnection $db,
        protected string $tenant_id,
        protected ?string $user_id = null,
        protected ?array $roles = null,
        private ?UserContext $userContext = null,
        private ?TenantContext $tenantContext = null,
        private ?RoleContext $roleContext = null
    ) {
        $this->userContext ??= new UserContext();
        $this->tenantContext ??= new TenantContext();
        $this->roleContext ??= new RoleContext();
    }

    abstract protected function table(): string;

    public function applyContext(?string $user_id = null, ?string $tenant_id = null, ?array $roles = null): void
    {
        if (isset($user_id)) {
            $this->userContext->apply($this->db, $user_id);
        }
        if (isset($tenant_id)) {
            $this->tenantContext->apply($this->db, $tenant_id);
        }
        if (isset($roles)) {
            $this->roleContext->apply($this->db, $roles);
        }
    }

    public function checkContext(): array
    {
        $result = $this->db->GetRow("SELECT 
            -- Local/Active session values (returns NULL if not set)
            current_setting('app.tenant_id', true) AS local_tenant_id,
            current_setting('app.user_id', true) AS local_user_id,
            current_setting('app.roles', true) AS local_roles,
            
            -- Global/Database persistent defaults (ignores session SET overrides)
            (SELECT boot_val FROM pg_settings WHERE name = 'app.tenant_id') AS global_tenant_id,
            (SELECT boot_val FROM pg_settings WHERE name = 'app.user_id') AS global_user_id,
            (SELECT boot_val FROM pg_settings WHERE name = 'app.roles') AS global_roles;
        ");


        return $result;
    }

    public function store(DataTransferObjectInterface|ModelInterface $data, ?array $returning = ['id', 'uuid'], ?string $table = null): StoreResponse|false|null
    {
        $table = $table ?? $this->table();
        $row = [];
        $this->applyContext($this->user_id, $this->tenant_id, $this->roles);
        foreach ($data->toArray() as $key => $value) {
            if ($key === 'id' || $key === 'password_confirmation' || $value === null) {
                continue; // skip: let DB defaults / serial handle it
            }

            if ($key === 'password' && password_needs_rehash($value, PASSWORD_BCRYPT, ['cost' => 12])) {
                $value = password_hash($value, PASSWORD_BCRYPT, ['cost' => 12]);
            } elseif (is_bool($value)) {
                $value = $value ? 1 : 0;
            } elseif ($value === 'on' || $value === '1') {
                $value = 1;
            } elseif (is_array($value)) {
                $value = Utils::PhpArrayToPg($value);
            }
            $row[$key] = $value;
        }

        $columns = implode(', ', array_keys($row));
        $placeholders = implode(', ', array_fill(0, count($row), '?'));

        $result = $this->db->Execute(
            "INSERT INTO $table ($columns) VALUES ($placeholders) RETURNING " . implode(', ', $returning),
            array_values($row)
        );
        
        if ($result === false) {
            return false;
        }

        $rows = $result->getRows();
        $row = $rows[0] ?? null;

        if ($row === null) {
            return false;
        }

        return new StoreResponse((int) $row['id'], $row['uuid'] ?? null, (int) $this->tenant_id);
    }



    public function update(DataTransferObjectInterface|ModelInterface $data, ?array $returning = ['id', 'uuid'], ?string $table = null): UpdateResponse|false|null
    {
        $table = $table ?? $this->table();
        $row = [];
        $this->applyContext($this->user_id, $this->tenant_id, $this->roles);
        foreach ($data->toArray() as $key => $value) {
            if ($key === 'id' || $key === 'password_confirmation' || $value === null || $key === 'uuid') {
                continue; // skip: let DB defaults / serial handle it
            }

            if ($key === 'password' && password_needs_rehash($value, PASSWORD_BCRYPT, ['cost' => 12])) {
                $value = password_hash($value, PASSWORD_BCRYPT, ['cost' => 12]);
            } elseif (is_bool($value)) {
                $value = $value ? 1 : 0;
            } elseif ($value === 'on' || $value === '1') {
                $value = 1;
            } elseif (is_array($value)) {
                $value = Utils::PhpArrayToPg($value);
            }
            $row[$key] = $value;
        }

        $columns = implode(', ', array_map(fn(string $key) => "$key = ?", array_keys($row)) );
        

        $returning = implode(', ', $returning);


        $result = $this->db->Execute(
            "UPDATE $table SET $columns WHERE uuid = ? RETURNING $returning",
            array_merge(array_values($row), [$data->uuid])
        );
        
        
        if ($result === false) {
            return false;
        }

        $row = $rows[0] ?? null;

        if ($row === null) {
            return false;
        }

        return new UpdateResponse((int) $row['id'], $row['uuid'] ?? null, (int) $this->tenant_id);
    }

    protected function scopedQuery(string $query = '', array $params = []): array|false
    {
        $this->db->StartTrans();
        try {
            $this->applyContext($this->user_id, $this->tenant_id, $this->roles);
            $result = $this->db->GetAll($query, $params);
            $this->db->CompleteTrans();
        } catch (\Throwable $e) {
            $this->db->FailTrans();
            $this->db->CompleteTrans();
            throw new TransactionFailedException('Transaction failed!', 500, $e);
        }
        
        return $result;
    }

    protected function buildSearchClause(array $columns, string $searchTerm): array
    {
        $conditions = array_map(
            fn(string $column) => "public.unaccent(lower({$column})) LIKE public.unaccent(lower(?))",
            $columns
        );

        return [
            'sql' => '(' . implode(' OR ', $conditions) . ')',
            'params' => array_fill(0, count($columns), '\'%' . $searchTerm . '\'%'),
        ];
    }

    public function fr2Arr(ADORecordSet|bool $recordSet, bool $bStoreOnRecords = false): false|array
    {
        if ($recordSet === false) {
            return false;
        }
        /** @var array */
        $results = [];

        $arrayColumns = $this->getArrayColumnsFromRecordSet($recordSet);

        while (!$recordSet->EOF) {
            $fields = $recordSet->fields;

            foreach ($arrayColumns as $col) {
                if (isset($fields[$col]) && is_string($fields[$col])) {
                    $fields[$col] = Utils::pgArrayToPhp($fields[$col]);
                }
            }

            if ($bStoreOnRecords) {
                $results['records'][] = $fields;
            } else {
                $results[] = $fields;
            }
            $recordSet->MoveNext();
        }

        return $results;
    }


    private function getArrayColumnsFromRecordSet(ADORecordSet $recordSet): array
    {
        $arrayColumns = [];
        $fieldCount = $recordSet->FieldCount();

        for ($i = 0; $i < $fieldCount; $i++) {
            $field = $recordSet->FetchField($i);
            // Postgres internally prefixes array types with "_" (e.g. _text, _int4, _varchar)
            if (isset($field->type) && str_starts_with($field->type, '_')) {
                $arrayColumns[] = $field->name;
            }
        }

        return $arrayColumns;
    }

    public function listAvailableTables(): array|object|false
    {
        $result = $this->db->GetAll('SELECT table_name FROM information_schema.tables WHERE table_schema=?', ['public']);
        return $result;
    }



}