<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\Container;

use App\Shared\Container\ContainerException;

final class Container
{
    /** @var array<string, mixed> Explicit bindings — for things Reflection can't build on its own */
    private array $bindings = [];

    /** @var array<string, mixed> Already-built singletons — built once per request, reused */
    private array $instances = [];
    private array $uncacheable = [];

    public function bind(string $abstract, object $concrete, bool $cache = true): void
    {
        $this->bindings[$abstract] = $concrete;
        $this->uncacheable[$abstract] = !$cache;
    }

    public function resolve(string $class): object
    {
        $isRepository = class_exists($class)
            && (is_subclass_of($class, \App\Shared\Domain\BaseRepository::class) || is_subclass_of($class, \App\Shared\Domain\BaseService::class));

        if (!$isRepository && isset($this->instances[$class]) && empty($this->uncacheable[$class])) {
            return $this->instances[$class];
        }

        if (isset($this->bindings[$class])) {
            $binding = $this->bindings[$class];
            $instance = $binding instanceof \Closure ? $binding($this) : $binding;

            if ($isRepository) {
                return $instance;
            }

            return $this->instances[$class] = $instance;
        }

        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            $instance = new $class();
            return $isRepository ? $instance : ($this->instances[$class] = $instance);
        }

        $arguments = array_map(
            fn(\ReflectionParameter $param) => $this->resolveParameter($param),
            $constructor->getParameters()
        );

        $instance = $reflection->newInstanceArgs($arguments);

        if ($isRepository) {
            return $instance;
        }

        return $this->instances[$class] = $instance;
    }
    private function resolveParameter(\ReflectionParameter $param): mixed
    {
        $type = $param->getType();

        if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
            return $this->resolve($type->getName());
        }
    
        // THIS branch — does your actual code have it?
        if (isset($this->bindings[$param->getName()])) {
            $binding = $this->bindings[$param->getName()];
            return $binding instanceof \Closure ? $binding($this) : $binding;
        }

        if ($param->isDefaultValueAvailable()) {
            return $param->getDefaultValue();
        }

        throw new ContainerException(
            "Cannot resolve parameter '\${$param->getName()}' for {$param->getDeclaringClass()?->getName()} — "
            . "no type-hint, no binding, and no default value."
        );
    }
}