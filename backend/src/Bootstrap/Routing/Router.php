<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Bootstrap\Routing;

use ReflectionAttribute;
use ReflectionMethod;
use ReflectionClass;
use ReflectionNamedType;
use App\Shared\Http\FileRateLimiter;
use App\Shared\Container\Container;
use App\Shared\Http\{Request,  Session};
use App\Infrastructure\Redis\RedisConnectionFactory;
use App\Shared\Http\Attributes\Route as RouteAttribute;
use App\Shared\Http\Attributes\Middleware as MiddlewareAttribute;
use App\Shared\Http\Attributes\RateLimit as RateLimitAttribute;
use App\Shared\Http\Attributes\Permission as PermissionAttribute;
use App\Infrastructure\Database\{ConnectionProvider, ConnectionFactory};
use App\Shared\Http\Interfaces\{RateLimiterInterface,MiddlewareInterface};
use App\Shared\Http\Middleware\{RateLimitMiddleware,PermissionMiddleware};
use App\Infrastructure\Database\RLS\{RoleContext, TenantContext, UserContext};
use App\Bootstrap\Routing\Exceptions\{RouteNotFoundException, MethodNotAllowedException};

final class Router {
   
    /**
     * @var array<int, array{
     *     methods: string[],
     *     pattern: string,
     *     paramNames: string[],
     *     controller: class-string,
     *     action: string,
     *     path: string,
     *     middlewares: class-string[],
     *     rateLimit: RateLimitAttribute|null,
     *     permissions: PermissionAttribute|null,
     *     injectsRequest: bool
     * }>
     */
    private array $routes = [];

    /** @var class-string[] */
    public array $globalMiddlewares = [];

    protected ?Container $container = null;

    function __construct(private readonly ?RateLimiterInterface $rateLimiter = new FileRateLimiter()) {
        $this->container = new Container();
    }

    public function bootContainerForRequest(Request $request): void
    {
        $connectionProvider = new ConnectionProvider(
            new ConnectionFactory(),
            new TenantContext(),
            new RoleContext(),
            new UserContext(),
            new RedisConnectionFactory(),
        );

        $this->container->bind(\ADOConnection::class, function () use ($connectionProvider, $request) {
            $tenant_id = $request->attribute('tenant_id');
            $user_id = $request->attribute('user_id');
            $roles = $request->attribute('roles');
            return $connectionProvider->get($tenant_id, $user_id, $roles);
        }, false);

        $this->container->bind('tenant_id', fn() => (string)$request->attribute('tenant_id'));
        $this->container->bind('user_id', fn() => (string)$request->attribute('user_id'));
        $this->container->bind('roles', fn() => $request->attribute('roles'));
        
        $this->container->bind(Session::class, fn() => new Session());
        $this->container->bind(Request::class, $request);
        
    }

    /**
     * @param class-string<MiddlewareInterface> $middlewareClass
     */
    function addGlobalMiddleware(string $middlewareClass) : void {
        $this->globalMiddlewares[] = $middlewareClass;
    }

    function registerControllers(array $controllers) : void {
        foreach($controllers as $controller) {
            $this->registerController($controller);
        }
    }

    function registerController(string $controller) : void {
        $reflection = new ReflectionClass($controller);

        $basePath = '';
        $classRouteAttributes = $reflection->getAttributes(RouteAttribute::class);
        if($classRouteAttributes !== []){
            /** @var RouteAttribute $classRoute */
            $classRoute = $classRouteAttributes[0]->newInstance();
            $basePath = $classRoute->path;
        }

        $classMiddleware = $reflection->getAttributes(MiddlewareAttribute::class);
        $classRateLimit = $reflection->getAttributes(RateLimitAttribute::class);
        $classPermissions = $reflection->getAttributes(PermissionAttribute::class);

        foreach($reflection->getMethods() as $method) {
            $methodRouteAttributes = $method->getAttributes(RouteAttribute::class);
            if($methodRouteAttributes === []){
                continue;
            }

            $methodMiddleware = $this->resolveMiddlewareClasses($method->getAttributes(MiddlewareAttribute::class));
            $methodRateLimit = $this->resolveRateLimit($method->getAttributes(RateLimitAttribute::class));
            $methodPermissions = $this->resolvePermissions($method->getAttributes(PermissionAttribute::class));
            $injectsRequest = $this->methodInjectsRequest($method);

            foreach($methodRouteAttributes as $attribute) {
                $route = $attribute->newInstance();
                $fullPath = $this->joinPaths($basePath, $route->path);
                [$pattern, $paramNames] = $this->compilePattern($fullPath);

                $this->routes[] = [
                    'methods' => $route->methods,
                    'pattern' => $pattern,
                    'paramNames' => $paramNames,
                    'controller' => $controller,
                    'action' => $method->getName(),
                    'path' => $fullPath,
                    'middlewareClasses' => [...$classMiddleware, ...$methodMiddleware],
                    'rateLimit' => $methodRateLimit ?? $classRateLimit,
                    'permissions' => $methodPermissions ?? $classPermissions,
                    'injectsRequest' => $injectsRequest
                ];
            }
        }

        $this->sortRoutes();
    }

    function dispatch(Request $request) : mixed {
        $method = strtoupper($request->method);
        $path = $request->path;

        $pathMatchedWrongMethod = false;
        $allowedMethodsForPath = [];
        foreach($this->routes as $route) {
            if(!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }
            if(!in_array($method, $route['methods'], true)) {
                $pathMatchedWrongMethod = true;
                $allowedMethodsForPath = $route['methods'];
                continue;
            }
            $params = [];
            foreach($route['paramNames'] as $name) {
                $params[$name] = $matches[$name] ?? null;
            }

            $request->routeParams = $params;

            return $this->runPipeline($route, $request);
        }

        if($pathMatchedWrongMethod) {
            throw new MethodNotAllowedException($method, $path, $allowedMethodsForPath);
        }


        throw new RouteNotFoundException($method, $path);
        return null;
    }

    function listRoutes() : array {
        return array_map(static fn (array $route) : string => implode('|', $route['methods']) . ' ' . $route['path'] . ' -> ' . $route['controller'] . '::' . $route['action'] . ' () ' 
        . ($route['rateLimit'] !== null ? ('Rate Limit: ' . ($route['rateLimit']->maxAttempts ?? '') . '/'. ($route['rateLimit']->decaySeconds ?? '') . 's') : '')
        . ($route['permissions'] !== null ? 'Permissions: ' . ($route['permissions'] ?? '')  : ''),
         $this->routes);
    }
function runPipeline(array $route, Request $request) : mixed {
    $this->bootContainerForRequest($request);

    $middlewareClasses = [...$this->globalMiddlewares];

    if (isset($route['rateLimit'], $route['rateLimit']->maxAttempts, $route['rateLimit']->decaySeconds) && $route['rateLimit'] !== null) {
        $rateLimitMiddleware = new RateLimitMiddleware($this->rateLimiter, $route['rateLimit']->maxAttempts, $route['rateLimit']->decaySeconds);
        $pipeline = static fn(Request $request) : mixed => $rateLimitMiddleware->handle($request, $pipeline);
    }
    

    foreach($route['middlewareClasses'] as $class) {
        $middlewareClasses[] = $class instanceof ReflectionAttribute
            ? $class->getArguments()[0]
            : $class;
    }

    $destination = function(Request $request) use ($route) : mixed {
        $controllerInstance = $this->container->resolve($route['controller']);
        $args = $route['injectsRequest'] ? [$request, ...$request->routeParams] : [...$request->routeParams];

        return $controllerInstance->{$route['action']}(...$args);
    };

    $pipeline = $destination;

    if (isset($route['permissions']) && empty($route['permissions']->permissions) === false) {
        // $permissionMiddleware = new PermissionMiddleware($route['permissions']);
        $permissionMiddleware = $this->container->resolve(PermissionMiddleware::class);
        $pipeline = static fn(Request $request) : mixed => $permissionMiddleware->handle($request, $pipeline, $route['permissions']->permissions);
    }

    $pipeline = array_reduce(
        array_reverse($middlewareClasses),
        function (\Closure $carry, string $middlewareClass) : \Closure {
            return function (Request $request) use ($carry, $middlewareClass) : mixed {
                $middleware = $this->container->resolve($middlewareClass);
                return $middleware->handle($request, $carry);
            };
        },
        $pipeline
    );

    return $pipeline($request);
}
    function resolveMiddlewareClasses(array $attributes) : array {
        return array_map(static function(ReflectionAttribute $attribute) : ?string {
            return $attribute->newInstance()->class;
        },$attributes);
    }

    function resolveRateLimit(array $attributes) : ?RateLimitAttribute {
        return $attributes===[] ? null : $attributes[0]->newInstance();
    }


    function resolvePermissions(array $attributes) : ?PermissionAttribute {
        return $attributes===[] ? null : $attributes[0]->newInstance();
    }

    function methodInjectsRequest(ReflectionMethod $method) : bool {
        $parameters = $method->getParameters();
        if($parameters === []) {
            return false;
        }

        $type = $parameters[0]->getType();
        
        return $type instanceof ReflectionNamedType && $type->getName() === Request::class;
    }

    private function sortRoutes() {
        usort($this->routes, static function(array $a, array $b) : int {
            $byParamCount = count($a['paramNames']) <=> count($b['paramNames']);
            if($byParamCount !== 0) {
                return $byParamCount;
            }

            return strlen($b['path']) <=> strlen($a['path']);
        });
    }

    /*
    *  @return array{0:string, 1: string[]}
    */
    private function compilePattern(string $path) : array {
        $paramNames = [];
 
        $pattern = preg_replace_callback(
            '#\{(\w+)\}#',
            static function (array $matches) use (&$paramNames): string {
                $paramNames[] = $matches[1];
                return '(?P<' . $matches[1] . '>[^/]+)';
            },
            $path
        );
 
        return ['#^' . $pattern . '$#', $paramNames];

    }

    private function joinPaths(string $base, string $path) : string {
        $joined = trim(trim($base, '/') . '/' . trim($path, '/'), '/');
 
        return '/' . $joined;

    }
}