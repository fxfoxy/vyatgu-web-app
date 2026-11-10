<?php declare(strict_types=1);

namespace App\Library\Api;

use App\Library\Api\Requests\AuthRequest;
use App\Library\Api\Requests\RegistrationRequest;
use App\Library\Api\Responses\UserResponseModel;
use App\Library\Api\View\JsonView;
use App\Library\Auth\UserAuth;
use App\Library\Framework\Routing\ControllerAbstract;
use App\Library\Framework\Routing\HttpMethodsEnum;
use App\Library\Framework\Routing\HttpRequest;
use App\Library\Framework\Routing\ViewAbstract;

final class FrontendController extends ControllerAbstract {
    public function registerAction(HttpRequest $request): ViewAbstract {
        $registrationRequest = new RegistrationRequest($request, HttpMethodsEnum::POST);
        $registrationRequest->validate();
        $registrationRequest->execute();

        return new JsonView(UserResponseModel::getInfo($registrationRequest->getUser()));
    }

    public function loginAction(HttpRequest $request): ViewAbstract {
        $authRequest = new AuthRequest($request, HttpMethodsEnum::POST);
        $authRequest->validate();
        $authRequest->execute();

        return new JsonView(UserResponseModel::getInfo($authRequest->getUser()));
    }

    public function logoutAction(HttpRequest $request): ViewAbstract {
        $request->requireHttpMethod(HttpMethodsEnum::GET);
        UserAuth::requireAuthUser();
        UserAuth::logout();
        return new JsonView([]);
    }
}
