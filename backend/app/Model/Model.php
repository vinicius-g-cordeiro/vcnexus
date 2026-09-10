<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Model;

use ADORecordSet;
use App\Database\Compilers\PostgreSQLSchemaCompiler;
use App\Database\Schema\Schema;
use App\DTOs\DTOInterface;
use App\Exceptions\AppExceptionHandler;
use App\Shared\Connection;
use App\Shared\Context\AuthContext;
use App\Shared\Session;
use DateTimeZone;
use DateTime;
use App\Shared\Helpers\Utils;

class Model extends Connection
{
    public ?PostgreSQLSchemaCompiler $sqlCompiler = null;

    private ?Session $session = null;

    function __construct($dbConnection = null, public ?Schema $schema = null)
    {
        parent::__construct();
        $this->schema = $schema;
        $this->sqlCompiler = new PostgreSQLSchemaCompiler($dbConnection, $this->schema);
        $this->session = Session::getInstance();
        if ($this->doesTableExists() === false) {
            $this->sqlCompiler->createTable();
        }

        $contextUserID = $this->getConnection()->Execute(
            "SELECT set_config('app.user_id', ?, true), set_config('app.tenant_id', ?, false)",
            [$this->session->get('user')->id ?? '', $this->session->get('user')->tenant_id ?? '']
        );
    }

    private function doesTableExists(): bool
    {

        if (isset($this->schema) === false) {
            throw new AppExceptionHandler('Table DTO was not set for model!', 500);
        }

        if ($this->schema->table === '') {
            return false;
        }

        $dbRes = $this->getConnection()->Execute('SELECT table_name FROM information_schema.tables WHERE table_schema=? AND table_name = ?', ['public', $this->schema->table]);

        $result = $this->fr2Arr($dbRes);

        return $result === false ? false : true;
    }

    private function listAvailableTables(): object|false
    {
        $dbRes = $this->getConnection()->Execute('SELECT table_name FROM information_schema.tables WHERE table_schema=?', ['public']);
        $result = (object) $this->fr2Arr($dbRes);

        return $result;
    }

    public function fr2Arr(ADORecordSet $recordSet, bool $bStoreOnRecords = false, string $returnType = 'array'): array|bool|object
    {
        /** @var array|bool */
        $results = false;

        $arrayColumns = $this->getArrayColumnsFromRecordSet($recordSet);

        while (!$recordSet->EOF) {
            $fields = $recordSet->fields;

            foreach ($arrayColumns as $col) {
                if (isset($fields[$col]) && is_string($fields[$col])) {
                    $fields[$col] = Utils::pgArrayToPhp($fields[$col]);
                }
            }

            if ($bStoreOnRecords) {
                $results['records'][] = (object) $fields;
            } else {
                $results[] = (object) $fields;
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


    function store(?DTOInterface $dataTransferObject): object|bool|int|string
    {


        $fields = [];
        foreach ($dataTransferObject as $key => $value) {
            if ($key == 'id') {
                continue;
            }

            if ($value == null) {
                continue;
            }

            if ($key == 'password' && password_needs_rehash($value, PASSWORD_BCRYPT, ['cost' => 12])) {
                $fields['password'] = password_hash($value, PASSWORD_BCRYPT, ['cost' => 12]);
                continue;
            }

            if ($key == 'password_confirmation') {
                continue;
            }

            if ($value == 'on' || $value == '1') {
                $fields[$key] = 1;
                continue;
            }

            if (is_array($value)) {
                $fields[$key] = '{' . implode(',', array_map(
                    fn(string $val) => '"' . str_replace('"', '\"', $val) . '"',
                    $value
                )) . '}';

                continue;
            }
            $fields[$key] = $value;
        }

        $date = new DateTime('now', new DateTimeZone('UTC'));
        $fields['created_by'] = 1;
        $fields['created_at'] = $date->getTimestamp();
        $fields['created_at_local'] = $date->setTimezone(new DateTimeZone('America/Sao_Paulo'))->getTimestamp();


        $return = $this->getConnection()->AutoExecute($this->schema->table, $fields, 'INSERT');
        if ($return === false) {
            throw new AppExceptionHandler('500 - Error', 500);
        }
        $saveResult = (object) $this->getConnection()->GetRow('SELECT tenant_id, uuid FROM ' . $this->schema->table . ' WHERE id = ? LIMIT 1 ', [$this->getConnection()->Insert_ID()]);
        return object(insertID: $this->getConnection()->Insert_ID(), tenant_id: $saveResult->tenant_id ?? null, uuid: $saveResult->uuid) ?: false;
    }


    function update(?DTOInterface $dataTransferObject, string $where, bool $bUpdate = true): object|bool|int
    {
        $fields = [];
        foreach ($dataTransferObject as $key => $value) {

            // Check if the key is 'id' and skip it as we don't want to update the id
            if ($key == 'id' || $key == 'uuid') {
                continue;
            }


            if ($value == null) {
                continue;
            }

            if ($key == 'password' && $value !== null && trim($value) !== '' && password_needs_rehash($value, PASSWORD_BCRYPT, ['cost' => 12])) {
                $fields['password'] = password_hash($value, PASSWORD_BCRYPT, ['cost' => 6]);
                continue;
            }

            if ($value == 'on' || $value == '1') {
                $fields[$key] = 1;
                continue;
            }

            if (is_array($value)) {
                $fields[$key] = '{' . implode(',', array_map(
                    fn(string $val) => '"' . str_replace('"', '\"', $val) . '"',
                    $value
                )) . '}';

                continue;
            }
            $fields[$key] = $value;
        }


        if (isset($this->session->get('user')->id)) {
            $date = new DateTime('now', new DateTimeZone('UTC'));
            $fields['updated_by'] = $this->session->get('user')->id;
            $fields['updated_at'] = $date->getTimestamp();
            $fields['updated_at_local'] = $date->setTimezone(new DateTimeZone('America/Sao_Paulo'))->getTimestamp();
        }

        if (empty($where)) {
            throw new AppExceptionHandler('No where provided for update clause', 500);
        }

        $return = $this->getConnection()->AutoExecute($this->schema->table, $fields, 'UPDATE', $where);

        // if($return === false){
        //     throw new RuntimeException('500 - Error', 500);
        // }

        $updatedID = (object) $this->getConnection()->GetRow('SELECT id FROM ' . $this->schema->table . ' WHERE ' . $where);

        return (int) $updatedID->id;
    }


    function deactivate(string $where): object|bool|int
    {
        $fields = [];

        $fields['active'] = 0;


        if (isset($this->session->get('user')->id)) {
            $date = new DateTime('now', new DateTimeZone('UTC'));
            $fields['deleted_by'] = $this->session->get('user')->id;
            $fields['deleted_at'] = $date->getTimestamp();
            $fields['deleted_at_local'] = $date->setTimezone(new DateTimeZone('America/Sao_Paulo'))->getTimestamp();
        }

        if (empty($where)) {
            throw new AppExceptionHandler('No where provided for update clause', 500);
        }

        $return = $this->getConnection()->AutoExecute($this->schema->table, $fields, 'UPDATE', $where);

        $updatedID = (object) $this->getConnection()->GetRow('SELECT id FROM ' . $this->schema->table . ' WHERE ' . $where);

        return (int) $updatedID->id;
    }


    function activate(string $where): object|bool|int
    {
        $fields = [];

        $fields['active'] = 1;
        $fields['deleted_by'] = null;
        $fields['deleted_at'] = null;
        $fields['deleted_at_local'] = null;

        if (empty($where)) {
            throw new AppExceptionHandler('No where provided for update clause', 500);
        }

        if (isset($this->session->get('user')->id)) {
            $date = new DateTime('now', new DateTimeZone('UTC'));
            $fields['updated_by'] = $this->session->get('user')->id;
            $fields['updated_at'] = $date->getTimestamp();
            $fields['updated_at_local'] = $date->setTimezone(new DateTimeZone('America/Sao_Paulo'))->getTimestamp();
        }

        $return = $this->getConnection()->AutoExecute($this->schema->table, $fields, 'UPDATE', $where);

        $updatedID = (object) $this->getConnection()->GetRow('SELECT id FROM ' . $this->schema->table . ' WHERE ' . $where);

        return (int) $updatedID->id;
    }


    function list(?object $parameters): object|bool|null|array
    {
        $response = null;
        if (isset($parameters->paginate) && $parameters->paginate === true) {
        } else {
            $this->getConnection()->Execute('select * from "' . $this->schema->table . '" ;');
        }

        return $response;
    }

    function block(string $where): object|bool|int
    {
        $fields = [];

        $fields['blocked'] = 1;
        if (isset($this->session->get('user')->id)) {
            $date = new DateTime('now', new DateTimeZone('UTC'));
            $fields['blocked_by'] = $this->session->get('user')->id;
            $fields['blocked_at'] = $date->getTimestamp();
            $fields['blocked_at_local'] = $date->setTimezone(new DateTimeZone('America/Sao_Paulo'))->getTimestamp();
        }

        if (empty($where)) {
            throw new AppExceptionHandler('No where provided for update clause', 500);
        }

        $return = $this->getConnection()->AutoExecute($this->schema->table, $fields, 'UPDATE', $where);

        $updatedID = (object) $this->getConnection()->GetRow('SELECT id FROM ' . $this->schema->table . ' WHERE ' . $where);

        return (int) $updatedID->id;
    }

    function unblock(string $where): object|bool|int
    {
        $fields = [];

        $fields['blocked'] = null;
        $fields['blocked_by'] = null;
        $fields['blocked_at'] = null;
        $fields['blocked_at_local'] = null;

        if (empty($where)) {
            throw new AppExceptionHandler('No where provided for update clause', 500);
        }

        $return = $this->getConnection()->AutoExecute($this->schema->table, $fields, 'UPDATE', $where);

        $updatedID = (object) $this->getConnection()->GetRow('SELECT id FROM ' . $this->schema->table . ' WHERE ' . $where);

        return (int) $updatedID->id;
    }

}