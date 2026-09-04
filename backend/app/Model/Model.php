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
use App\Shared\Session;
use DateTimeZone;
use DateTime;
use RuntimeException;

class Model extends Connection
{
    private ?PostgreSQLSchemaCompiler $sqlCompiler = null;

    private ?Session $session = null;

    function __construct($dbConnection = null, public ?Schema $schema = null) {
        parent::__construct($dbConnection);
        
        $this->sqlCompiler = new PostgreSQLSchemaCompiler($dbConnection, $schema);
        $this->session = Session::getInstance();
        if ($this->doesTableExists() === false) {
            $this->sqlCompiler->createTable();
        }
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
        /** @var array|bool  */
        $results = false;
        while (!$recordSet->EOF) {
            if ($bStoreOnRecords) {
                $results['records'][] = (object)$recordSet->fields;
            } else {
                $results[] = (object)$recordSet->fields;
            }
            $recordSet->MoveNext();
        }

        return $results;
    }

    
    function store(?DTOInterface $dataTransferObject): object|bool|int|string {

        
        $fields = [];
        foreach ($dataTransferObject as $key => $value) {
            if ($key == 'id') {
                continue;
            }

            if($value == null) { continue; }

            if ($key == 'password' && password_needs_rehash($value, CRYPT_SHA512, ['cost' => 12])) {
                $fields['password'] = password_hash($value, CRYPT_SHA512, ['cost' => 12]);
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
        if($return === false){
            throw new AppExceptionHandler('500 - Error', 500);
        }
        $tenant_id = (object)$this->getConnection()->GetRow('SELECT tenant_id FROM ' . $this->schema->table . ' WHERE id = ?', [$this->getConnection()->Insert_ID()]);
        return object(insertID : $this->getConnection()->Insert_ID(), tenant_id: $tenant_id->tenant_id ?? null) ?: false;
    }


    function update(?DTOInterface $dataTransferObject, string $where): object|bool|int {
        $fields = [];
        foreach ($dataTransferObject as $key => $value) {

            // Check if the key is 'id' and skip it as we don't want to update the id
            if ($key == 'id') {
                continue;
            }


            if($value == null){ continue; }

            if ($key == 'password' && $value !== null && trim($value) !== '' && password_needs_rehash($value, CRYPT_SHA512, ['cost' => 12])) {
                $fields['password'] = password_hash($value, CRYPT_SHA512, ['cost' => 6]);
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

        if(empty($where)){
            throw new AppExceptionHandler('No where provided for update clause', 500);
        }

        $return = $this->getConnection()->AutoExecute($this->schema->table, $fields, 'UPDATE', $where);


        if($return === false){
            throw new RuntimeException('500 - Error', 500);
        }


        return $this->getConnection()->Affected_Rows();
    }

    function list(?object $parameters) : object|bool|null|array {
        $response = null;
        if(isset($parameters->paginate) && $parameters->paginate === true) {
        }else{
            $this->getConnection()->Execute('select * from "' . $this->schema->table . '" ;');
        }

        return $response;
    }


}