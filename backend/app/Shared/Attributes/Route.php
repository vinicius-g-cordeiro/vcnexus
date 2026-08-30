<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Shared\Attributes;


use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class Route {

    /** @var string[] */
    public readonly array $methods;
    
    /** @var string */
    public readonly string $path;
    public function __construct(string|array $methods = 'GET', string $path = '/') {
        $rawMethods = is_array($methods) ? $methods : preg_split('/[|,]/', $methods);

        $this->methods = array_values(array_map(
            static fn (string $inMethod): string => strtoupper(trim($inMethod)),
            $rawMethods
        ));

        $this->path = '/' . trim($path, '/');
    }
}