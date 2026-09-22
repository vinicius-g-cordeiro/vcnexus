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
 * #[Timestamps(createdAt: 'created_at', updatedAt: 'updated_at', deletedAt: null)] 
 * final class UserSchema {
 * }
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Auditable {

    #[Column(type: 'bigint', default: null)]
    #[Nullable(nullable: true)]
    public ?string $created_by;

    #[Column(type: 'bigint', default: null)]
    #[Nullable(nullable: true)]
    public ?string $updated_by;

    #[Column(type: 'bigint', default: null)]
    #[Nullable(nullable: true)]
    public ?string $deleted_by;

    #[Column(type: 'varchar', length: 500, default: null)]
    #[Nullable(nullable: true)]
    public ?string $deleted_reason;

    /**
     * Auditable is the attribute to handle the `created_by`/`updated_by`/`deleted_by`/`deleted_reason` columns in the database. if present it will generate the columns 
     * @param ?string $created_by 
     * @param ?string $updated_by
     * @param ?string $deleted_by
     * @param ?string $deleted_reason 
     */
    public function __construct(?string $created_by = 'created_by', ?string $updated_by = 'updated_by', ?string $deleted_by = null, ?string $deleted_reason = null) {
        if(is_null($created_by)){
            unset($this->created_by);
        }else{
            $this->created_by = $created_by;
        }

        if(is_null($updated_by)){
            unset($this->updated_by);
        }else{
            $this->updated_by = $updated_by;
        }

        if(is_null($deleted_by)){
            unset($this->deleted_by);
        }else{
            $this->deleted_by = $deleted_by;
        }

        if(is_null($deleted_reason)){
            unset($this->deleted_reason);
        }else{
            $this->deleted_reason = $deleted_reason;
        }
    }
}