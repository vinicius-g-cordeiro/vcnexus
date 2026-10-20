<?php

/**
 * @brief
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/09/14
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace Tools\SchemaCompiler;

final class MigrationSequence
{
    private const PATH = 'database/migrations/.sequence.json';

    /** @var array<string, int> table name => assigned number */
    private array $assignments;

    public function __construct()
    {
        $this->assignments = file_exists(self::PATH)
            ? json_decode(file_get_contents(self::PATH), true)
            : [];
    }

    /**
     * Returns the permanent number for this table — assigning a new one
     * (highest existing + 1) only if this table has never been compiled before.
     */
    public function numberFor(string $table): int
    {
        if (isset($this->assignments[$table])) {
            return $this->assignments[$table];
        }

        $next = empty($this->assignments) ? 1 : max($this->assignments) + 1;
        $this->assignments[$table] = $next;
        $this->save();

        return $next;
    }

    private function save(): void
    {
        file_put_contents(self::PATH, json_encode($this->assignments, JSON_PRETTY_PRINT));
    }
}