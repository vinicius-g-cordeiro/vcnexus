<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared;


use App\Shared\Response;
use App\Shared\Attributes\Route as RouteAttribute;
use App\Shared\Attributes\Middleware as MiddlewareAttribute;
use App\Shared\Attributes\RateLimit as RateLimitAttribute;
use App\Shared\Request;
use ReflectionAttribute;
use ReflectionMethod;
use ReflectionClass;
use ReflectionNamedType;
use App\Shared\RateLimiting\RateLimiterInterface;
use App\Shared\RateLimiting\FileRateLimiter;
use App\Shared\Interfaces\MiddlewareInterface;
use App\Middleware\RateLimitMiddleware;


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
     *     injectsRequest: bool
     * }>
     */
    private array $routes = [];

    /** @var class-string[] */
    private array $globalMiddlewares = [];

    protected ?Connection $connection = null;

    function __construct(private readonly ?RateLimiterInterface $rateLimiter = new FileRateLimiter()) {
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

        foreach($reflection->getMethods() as $method) {
            $methodRouteAttributes = $method->getAttributes(RouteAttribute::class);
            if($methodRouteAttributes === []){
                continue;
            }

            $methodMiddleware = $this->resolveMiddlewareClasses($method->getAttributes(MiddlewareAttribute::class));
            $methodRateLimit = $this->resolveRateLimit($method->getAttributes(RateLimitAttribute::class));
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
    
        foreach($this->routes as $route) {
            if(!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }
            if(!in_array($method, $route['methods'], true)) {
                $pathMatchedWrongMethod = true;
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
            Response::json(message: sprintf('405 - Method not allowed: %s %s', $method, $path), status: false, code: 405, bShouldExit: true);
        }


        Response::json(message: sprintf('404 - Not found: %s %s', $method , $path), status: false, code: 404, bShouldExit: true);
        return null;
    }

    function listRoutes() : array {
        return array_map(static fn (array $route) : string => implode('|', $route['methods']) . ' ' . $route['path'] . ' -> ' . $route['controller'] . '::' . $route['action'] . ' () ' 
        . ($route['rateLimit'] !== null ? ('Rate Limit: ' . ($route['rateLimit']->maxAttempts ?? '') . '/'. ($route['rateLimit']->decaySeconds ?? '') . 's') : ''), $this->routes);
    }

    function runPipeline(array $route, Request $request) : mixed {
        $middlewareInstances = array_map(static fn(string $class) : MiddlewareInterface => new $class(), $this->globalMiddlewares);

        if(isset($route['rateLimit'], $route['rateLimit']->maxAttempts, $route['rateLimit']->decaySeconds) && $route['rateLimit'] !== null) {
            $middlewareInstances[] = new RateLimitMiddleware($this->rateLimiter, $route['rateLimit']->maxAttempts, $route['rateLimit']->decaySeconds);
        }

        foreach($route['middlewareClasses'] as $class) {
            $middlewareInstances[] = new $class();
        }

        $destination = static function(Request $request) use ($route) : mixed {
            $controllerInstance = new $route['controller']();
            $args = $route['injectsRequest'] ? [$request, ...$request->routeParams] : [...$request->routeParams];

            
            return $controllerInstance->{$route['action']}(...$args);
        };


        $pipeline = array_reduce(
            array_reverse($middlewareInstances),
            static fn(mixed $carry, MiddlewareInterface $middleware) : \Closure => 
                static fn(Request $request) : mixed => $middleware->handle($request, $carry),
            $destination
        );

        return $pipeline($request);
    }

    function resolveMiddlewareClasses(array $attributes) : array {
        return array_map(static fn(ReflectionAttribute $attribute) : string => $attribute->newInstance()->class, $attributes);
    }

    function resolveRateLimit(array $attributes) : ?RateLimitAttribute {
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