<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Modules\Authentication\Services;

use App\Modules\Authentication\DTOs\{LoginRequest, LogoutRequest, LoginResponse, AuthenticatedUserResponse};
use App\Modules\Authentication\Exceptions\InvalidCredentialsException;
use App\Modules\Authentication\Repositories\{AuthenticationRepository};
use App\Shared\Domain\BaseService;
use App\Infrastructure\Database\Hydrator;
use App\Shared\Http\{Request, Session};

final class AuthenticationService extends BaseService
{
     /**
     * @var string - Empty password just so we check against if the user is not found, so we don't expose the existence of the user or absence if someone is watching the response time of the endpoint
     */
    public string $hashedEmptyPassword = '$2y$12$KmWbgf9Q0HFyVgwQtBvy8.oIWLuVyE3rVSxnYLu88elM.TCxpmAZy';


    public function __construct(\ADOConnection $db, ?string $tenant_id, ?string $user_id, private Request $request, private Session $session, private AuthenticationRepository $authenticationRepository)
    {
        parent::__construct($db,$tenant_id,$user_id);
    }

    public function getAuthenticatedUser() : ?AuthenticatedUserResponse {        

        if($this?->session->get('user') === null) {
            return null;
        }
        
        return Hydrator::hydrate(AuthenticatedUserResponse::class, $this?->session->get('user')?->toArray() ?? []);
    }

    public function login(LoginRequest $loginRequest) : ?LoginResponse {
        /// @TODO validation of loginRequest fields

        $user = $this->authenticationRepository->findUserAccountUnscoped($loginRequest->login);
        
        if(isset($user) === false || $user === false) {
            // Verify password with empty password placeholder in order to avoid exposing info based on response time of the endpoint
            password_verify($loginRequest->password, $this->hashedEmptyPassword);
            throw new InvalidCredentialsException(); // But actually we didn't find the user in the database, avoiding exposing the existence of the user on the endpoint
        }

        
        if(password_verify($loginRequest->password, $user->password) === false) {
            throw new InvalidCredentialsException();
        }


        if($user->blocked === 1) {
            throw new InvalidCredentialsException();
        }

        // We have the user now we check the tenant context of it
        $userContext = $this->authenticationRepository->getUserTenants($user->id);
        
        
        if(isset($userContext) === false || $userContext === false) {
            throw new InvalidCredentialsException();
        }

        // Check if the user has a valid tenant, and if is not multiple 
        if(count($userContext->tenants) > 1) {
            // @todo Implement multi-tenant authentication
            throw new InvalidCredentialsException();
        }

        $loginResponse = Hydrator::hydrate(LoginResponse::class, $userContext->toArray()); 

        $this->session->set('user', $loginResponse);

        return $loginResponse;
    }

    public function logout(LogoutRequest $logoutRequest) : ?bool {
        
        if($logoutRequest->uuid !== $this->session->get('user')->uuid) {
            throw new InvalidCredentialsException();
        }
        $this->session->set('user', null);
        return true;
    }
}