<?php 
/** 
* @brief 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com><https://github.com/vinicius-g-cordeiro>
 * @version 1.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com> <https://github.com/vinicius-g-cordeiro>
 */

declare(strict_types=1);

namespace App\Controller;

use App\Shared\Request;
use App\Shared\Session;
use App\Shared\Connection;


class Controller { 
    public function __construct(protected ?Connection $dbConnection = null, protected readonly ?Request $request = null, protected readonly ?Session $session = null ) {
        
    }
}

