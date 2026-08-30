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
use App\Shared\Connection;

use App\Database\Schema\Column;
use App\Database\Schema\Index;
use App\Database\Schema\Constraint;
use App\DTOs\Users\WorkersDTO;

class Model extends Connection
{

    public $tableDTO;

    function __construct($dbConnection = null)
    {
        parent::__construct($dbConnection);
        $this->tableDTO = new WorkersDTO();
    }

    private function doesTableExists(): bool
    {
        if ($this->tableDTO->table == '') {
            return false;
        }

        $dbRes = $this->getConnection()->Execute('SELECT table_name FROM information_schema.tables WHERE table_schema=? AND table_name = ?', ['public', $this->tableDTO->table]);

        $result = $this->fr2Arr($dbRes);

        return $result === false ? false : true;
    }

    private function listAvailableTables(): object|false
    {
        $dbRes = $this->getConnection()->Execute('SELECT table_name FROM information_schema.tables WHERE table_schema=?', ['public']);
        $result = (object) $this->fr2Arr($dbRes);

        return $result;
    }

    public function createTable()
    {
        $tableName = $this->tableDTO->table;
        $createQuery = 'CREATE TABLE IF NOT EXISTS "' . $this->tableDTO->table . '" (';
        $createQuery .= implode("\r\n", array_map(static function ($val): string {
            if ($val instanceof Column) {

                $default = '';
                if (isset($val->default) && $val->default !== '') {
                    if (is_string($val->default)) {
                        $alreadyHasDefaultKeyword = preg_match('(DEFAULT)', $val->default);
                        $default = (($alreadyHasDefaultKeyword != 1 ? 'DEFAULT ' : ' ') . $val->default . ' ');
                    } else {
                        $default = 'DEFAULT ' . $val->default;
                    }
                }
                $value = '"' . $val->name . '" ' . $val->type . ' ' . ($val->isNull === false ? 'NOT NULL' : '') . ' ' .
                    $default;

                return $value . ",";
            }

            return '';
        }, get_object_vars($this->tableDTO)));

        $createQuery .= "\r\n\r\n" . 'PRIMARY KEY ("' . $this->tableDTO->id->name . '") ' . "\r\n\r\n";

        $constraints = implode("\r\n", array_map(static function ($val) use (&$tableName): string {
            if ($val instanceof Constraint) {
                $constraint = '';
                if ($val->bIsUnique === true) {
                    $constraint = 'CONSTRAINT "' . $val->name . '" UNIQUE (' . implode(',', array_map(static function ($c): string {
                        return '"' . $c . '"';
                    }, $val->uniqueColumns)) . "),";
                } else {
                    $constraint = 'CONSTRAINT "' . $val->name . '" FOREIGN KEY (' . implode(',', array_map(static function ($c): string {
                        return '"' . $c . '"';
                    }, $val->foreignKeys)) . ') REFERENCES "' . $val->references . '" (' . implode(',', array_map(static function ($c): string {
                        return '"' . $c . '"';
                    }, $val->columns)) . ') ';

                    if ($val->actionOnUpdate === false) {
                        $constraint .= 'ON UPDATE NO ACTION';
                    }

                    if ($val->actionOnDelete === true) {
                        $constraint .= ' ON DELETE ' . $val->deleteAction . ",";
                    }
                }

                return $constraint;
            }
            return '';
        }, get_object_vars($this->tableDTO->constraints)));

        if(trim($constraints) != ''){
            $createQuery .= ',';
        }
        $createQuery .= rtrim($constraints, ",") . "\r\n";
        $createQuery .= ");\r\n";

        $indexes = implode('', array_map(static function ($val) use (&$tableName): string {
            if ($val instanceof Index) {
                $index = 'CREATE ';
                if ($val->unique === true) {
                    $index .= 'UNIQUE ';
                }

                $index .= 'INDEX IF NOT EXISTS "' . $val->name . '" ON "' . $val->references . '" (' . implode(',', array_map(static function ($cols): string {
                    return '"' . $cols . '"';
                }, $val->columns)) . ") ";

                if (isset($val->condition) && !empty($val->condition)) {
                    $index .= ' WHERE ';
                    foreach ($val->condition as $key => $value) {
                        $index .= '"' . $key . '" = ' . $value;
                    }
                }

                return $index . ";\r\n";
            }
            return '';
        }, get_object_vars($this->tableDTO->indexes)));

        $createQuery .= "\r\n" . $indexes;

        $comments = implode('', array_map(static function ($val) use (&$tableName): string {
            if ($val instanceof Column) {
                $comment = '';
                if (isset($val->comment) && $val->comment !== '') {
                    $comment = 'COMMENT ON COLUMN "' . $tableName . '"."' . $val->name . '" IS \'' . $val->comment . "'; \r\n";
                }

                return $comment;
            }
            return '';
        }, get_object_vars($this->tableDTO)));

        $createQuery .= "\r\n" . $comments;

        try {
            
            
            $exec = $this->getConnection()->Execute($createQuery);
            echo '<pre>';
            echo $createQuery;
            echo '</pre>';

            dump($exec);
        } catch (\Throwable $th) {
            dd($th);
        }
        exit;
    }

    public function fr2Arr(ADORecordSet $recordSet, bool $bStoreOnRecords = false): array|bool
    {
        /** @var array|bool  */
        $results = false;
        while (!$recordSet->EOF) {
            if ($bStoreOnRecords) {
                $results['records'][] = (object) $recordSet->fields;
            } else {
                $results[] = (object) $recordSet->fields;
            }
            $recordSet->MoveNext();
        }

        return $results;
    }

}