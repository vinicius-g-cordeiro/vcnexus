<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\Schema\Attributes;

use Attribute;

/**
 * #[Timestamps(created_at: 'created_at', updated_at: 'updated_at', deleted_at: null)] 
 * final class UserSchema {
 * }
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Timestamps {

    #[Column(type: 'timestamp', default: 'CURRENT_TIMESTAMP', precision: null, scale: null)]
    #[Nullable(nullable: true)]
    public ?string $created_at = null;

    #[Column(type: 'timestamp', default:null, precision: null, scale: null)]
    #[Nullable(nullable: true)]
    public ?string $updated_at = null;

    #[Column(type: 'timestamp', default:null, precision: null, scale: null)]
    #[Nullable(nullable: true)]
    public ?string $deleted_at = null;
    
    /**
     * Timestamps attribute will be used to generate the `created_at` and `updated_at` columns in the database if the timestamp attribute is present and if the deleted_at property is not null it will also generate the `deleted_at` column
     * @param ?string $created_at 
     * @param ?string $updated_at
     * @param ?string $deleted_at
     */
    public function __construct(?string $created_at = null,?string $updated_at = null,?string $deleted_at = null) {
        if(is_null($created_at)){
            unset($this->created_at);
        }else{
            $this->created_at = $created_at;
        }

        if(is_null($updated_at)){
            unset($this->updated_at);
        }else{
            $this->updated_at = $updated_at;
        }

        if(is_null($deleted_at)){
            unset($this->deleted_at);
        }else{
            $this->deleted_at = $deleted_at;
        }
    }
}