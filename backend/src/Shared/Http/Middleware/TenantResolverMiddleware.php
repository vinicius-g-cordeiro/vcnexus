<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\Http\Middleware;

use App\Modules\Users\DTOs\UserTenantMembershipResponse;
use App\Modules\Users\Repositories\UserTenantMembershipRepository;
use App\Shared\Http\{Request, Response};
use App\Shared\Http\Interfaces\MiddlewareInterface;
use App\Modules\Users\Repositories\UserProfileRepository;
use App\Modules\Tenant\Repositories\TenantRepository;

final class TenantResolverMiddleware implements MiddlewareInterface
{

    public function __construct(
        protected ?UserProfileRepository $userRepository = null,
        protected ?UserTenantMembershipRepository $userTenantMembershipRepository = null,
        protected ?TenantRepository $tenantRepository = null,
    ) {}

    public function handle(Request $request, ?callable $next = null, ...$arguments): ?Response
    {
        $tenant_id = $this->resolveTenantId($request);
        if ($tenant_id === null) {
            // Never proceed with a null tenant — better a clear 400 than a silent RLS gap.
            return Response::json(message: 'Tenant not found for this request', data: [])->send(400, [], true);
        }

        $request = $request->withAttribute('roles', $this->resolveRoles($request));
        
        $request = $request->withAttribute('tenant_id', $tenant_id);
        return $next($request);
    }

    private function resolveTenantId(Request $request): ?string
    {
        return $this->resolveFromHost($request) ?? $this->resolveFromUser($request)->tenant_id;
    }


    private function resolveRoles(Request $request): ?array
    {
        return $this->resolveFromUser($request)->roles;
    }

    private function resolveFromHost(Request $request): ?string
    {
        $host = $request->headers['Host'] ?? '';
        $subdomain = explode('.', $host)[0] ?? null;

        if ($subdomain === null || $subdomain === 'www') {
            return null; // no subdomain, or a non-tenant host — fall through to user-based resolution
        }

        return $this->tenantRepository->findIdBySlugUnscoped($subdomain);
    }

    private function resolveFromUser(Request $request): ?UserTenantMembershipResponse
    {
        $user_id = $request->attribute('user_id'); // set by AuthMiddleware, which must run first
        
        if ($user_id === null) {
            return null;
        }

        $user = $this->userTenantMembershipRepository->findByIdUnscoped((string)$user_id);
        return $user;
    }
}