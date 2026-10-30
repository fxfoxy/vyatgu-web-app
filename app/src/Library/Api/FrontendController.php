<?php declare(strict_types=1);

namespace App\Library\Api;

use App\Library\Api\View\JsonView;
use App\Library\Framework\Routing\ControllerAbstract;
use App\Library\Framework\Routing\Request;
use App\Library\Framework\Routing\ViewAbstract;

final class FrontendController extends ControllerAbstract {
    public function registerAction(Request $request): ViewAbstract {
        return new JsonView(['aaa' => 111, 'bbb' => 'view']);
    }
}
