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
use App\Service\Service;
use App\Shared\Attributes\Route;

class Controller { 

    protected ?Service $service = null;
    public function __construct(protected ?Connection $dbConnection = null, public ?Request $request = null, protected ?Session $session = null ) {
        
    }

    #[Route('GET', '/health-check')]
    public function verifyConnections() : void {
        $this->service = new Service($this->dbConnection);
        $this->service->verifyHealth();
    }
}

