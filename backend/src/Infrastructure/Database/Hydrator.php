<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Shared\Http\Response;
use App\Shared\Helpers\Utils;

final class Hydrator
{
    /** @var array<class-string, list<array{name: string, column: string, type: ?string, nullable: bool, builtin: bool, hasDefault: bool}>> */
    private static array $cache = [];

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string, mixed> $row
     * @return T
     * @throws \RuntimeException
     */
    public static function hydrate(string $class, array $row): object
    {
        $args = [];

        foreach (self::params($class) as $p) {
            // Accept either camelCase or snake_case keys from the row
            $key = array_key_exists($p['name'], $row) ? $p['name']
                 : (array_key_exists($p['column'], $row) ? $p['column'] : null);

            if ($key === null) {
                if ($p['hasDefault']) {
                    continue; // let the constructor default apply
                }
                if ($p['nullable']) {
                    $args[$p['name']] = null;
                    continue;
                }
                throw new \RuntimeException(
                    "Missing column '{$p['column']}' for {$class}::\${$p['name']}", 500
                );
            }

            $args[$p['name']] = self::cast($row[$key], $p, $class);
        }

        return new $class(...$args);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param iterable<array<string, mixed>> $rows
     * @return list<T>
     */
    public static function hydrateMany(string $class, iterable $rows): array
    {
        $out = [];
        foreach ($rows as $row) {
            try{
            $out[] = self::hydrate($class, $row);
            }catch(\Exception $e){
                Response::log(file: 'error', message: $e->getMessage() . "\r\n" . $e->getTraceAsString() . "\r\n" . json_encode($e, JSON_PRETTY_PRINT), status: $e->getCode(), success: false);
                throw $e;
            }
        }
        return $out;
    }

    private static function cast(mixed $value, array $p, string $class): mixed
    {
        if ($value === null) {
            if (!$p['nullable']) {
                throw new \RuntimeException(
                    "NULL for non-nullable {$class}::\${$p['name']}", 500
                );
            }
            return null;
        }

        $type = $p['type'];

        // Non-builtin types: DateTime*, backed enums, or pass through
        if (!$p['builtin']) {
            if ($type === null || $value instanceof $type) {
                return $value;
            }
            if (is_a($type, \DateTimeInterface::class, true)) {
                return new $type((string) $value);
            }
            if (is_a($type, \BackedEnum::class, true)) {
                return $type::from($value);
            }
            return $value;
        }

        return match ($type) {
            'int'    => (int) $value,
            'float'  => (float) $value,
            'string' => (string) $value,
            'bool'   => self::toBool($value),
            'array'  => is_array($value) ? $value : Utils::pgArrayToPhp($value),
            default  => $value, // mixed, iterable, etc.
        };
    }

    private static function toBool(mixed $v): bool
    {
        if (is_bool($v)) {
            return $v;
        }
        // pgsql returns 't'/'f'; (bool)'f' would wrongly be true
        return in_array($v, ['t', 'true', '1', 1, 'y', 'yes'], true);
    }

    /** @return list<array{name: string, column: string, type: ?string, nullable: bool, builtin: bool, hasDefault: bool}> */
    private static function params(string $class): array
    {
        if (isset(self::$cache[$class])) {
            return self::$cache[$class];
        }

        $ctor = (new \ReflectionClass($class))->getConstructor();
        $params = [];

        foreach ($ctor?->getParameters() ?? [] as $param) {
            $type = $param->getType();
            $name = $param->getName();

            $params[] = [
                'name'       => $name,
                'column'     => strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name)),
                'type'       => $type instanceof \ReflectionNamedType ? $type->getName() : null,
                'nullable'   => $type === null || $type->allowsNull(),
                'builtin'    => $type instanceof \ReflectionNamedType && $type->isBuiltin(),
                'hasDefault' => $param->isDefaultValueAvailable(),
            ];
        }

        return self::$cache[$class] = $params;
    }
}