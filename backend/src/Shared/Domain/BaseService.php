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

use App\Shared\Domain\Exceptions\TransactionFailedException;

abstract class BaseService
{

    public function __construct(protected \ADOConnection $db, protected ?string $tenant_id = null, protected ?string $user_id = null, protected ?array $roles = null ) {}

    /**
     * 
     * @param callable $operation
     * @throws \Exception
     * @return mixed
     */
    protected function transactional(callable $operation) : mixed
    {
        $this->db->StartTrans();
        try{
            $result = $operation();

            if($this->db->HasFailedTrans()){
                $this->db->CompleteTrans();
                throw new TransactionFailedException($this->db->ErrorMsg());
            }
            
        }catch(\Throwable $th){
            $this->db->FailTrans();
            throw $th;
        }finally{
            $this->db->CompleteTrans();
        }

        return $result;
    }
    
}