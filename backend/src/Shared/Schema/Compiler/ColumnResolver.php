<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\Schema\Compiler;

use App\Shared\Schema\Attributes\{Column};



final class ColumnResolver
{
    public function __construct() {}

     /** @return Column[] */
    public function resolveFromColumnAttributes(string $modelClass): array
    {
        $columns = AttributeReader::getColumns($modelClass);
        return $columns;
    }


   /** @return Column[] — full column set: real properties + Timestamps + Auditable columns */
    public function resolveColumns(string $modelClass): array
    {
        $columns = $this->resolveFromColumnAttributes($modelClass);

        if ($timestamps = AttributeReader::getTimestamps($modelClass)) {
            foreach($timestamps as $key => $value) {
                if($value !== null){
                    $columns[$key] = $value;
                }
            }
        }

        if ($auditable = AttributeReader::getAuditable($modelClass)) {
            foreach($auditable as $key => $value) {
                if($value !== null){
                    $columns[$key] = $value;
                }
            }   
        }

        if($tenantScoped = AttributeReader::getTenantScoped($modelClass)){
            foreach($tenantScoped as $key => $value) {
                if($value !== null){
                    $columns[$key] = $value;
                }
            }
        }

        return $columns;
    }


}

