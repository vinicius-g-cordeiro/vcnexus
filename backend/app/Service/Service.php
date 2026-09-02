<?php
/** 
 * @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Service;

use App\Model\Model;
use App\Shared\Connection;
use App\Shared\Request;

use App\Shared\Session;
use App\Shared\Response;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;
use Closure;
use RuntimeException;

class Service
{

    protected ?Request $request = null;

    protected ?ValidatorInterface $validator = null;
    function __construct(protected ?Connection $connection = null, protected ?Model $model = null, protected ?Session $session = null)
    {
        $this->connection = $connection ?: Connection::getInstance();
        $this->model = $model ?: new Model();
        $this->validator = Validation::createValidator();
        $this->session = $session ?: Session::getInstance();
        $this->request = Request::instance();
    }

    function verifyHealth(): void
    {
        $bIsDatabaseConnected = $this->model->isConnected();
        $bIsRedisConnected = false;
        $bIsSessionAvailable = $this->session->isSessionValid();
        Response::json(message: 'Connected to API, Connections verified', code: 200, status: true, data: object(DatabaseConnection: $bIsDatabaseConnected, RedisConnection: $bIsRedisConnected, SessionValid: $bIsSessionAvailable));
    }


    protected function transaction(Closure $callback): mixed
    {
        $this->model->getConnection()->StartTrans();

        try {
            $result = $callback();

            if ($this->model->getConnection()->HasFailedTrans()) {
                throw new RuntimeException('Transaction failed.');
            }

            $this->model->getConnection()->CompleteTrans();

            return $result;

        } catch (Throwable $e) {
            $this->model->getConnection()->FailTrans();
            $this->model->getConnection()->CompleteTrans();

            throw $e;
        }
    }
}