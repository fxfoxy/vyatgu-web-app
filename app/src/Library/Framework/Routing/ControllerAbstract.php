<?php declare(strict_types=1);

namespace App\Library\Framework\Routing;

use App\Library\Api\View\JsonView;
use App\Library\Exceptions\RoutingException;
use App\Library\Framework\Routing\Generated\FeatureControllerMap;
use App\Library\Framework\Utils;
use App\Library\Super;
use LogicException;
use Throwable;

class ControllerAbstract {
    public static function processRequest(): void {
        $request = Request::createFromGlobals();

        $currentControllerClassName = self::getCurrentControllerClassName($request);
        $actionName                 = $request->action;

        try {
            if (!$currentControllerClassName || !class_exists($currentControllerClassName) || !method_exists($currentControllerClassName, $actionName)) {
                throw new RoutingException("This combination of class '{$currentControllerClassName}' and method '{$request->action}' does not exist");
            }

            $actionResult = (new $currentControllerClassName())->$actionName($request);
            if (!$actionResult instanceof ViewAbstract) {
                throw new LogicException("Controller method '{$currentControllerClassName}::{$actionName}()' did not return a ViewAbstract object");
            }
        } catch (Throwable $t) {
            Super::log()->warning($t->getMessage());
            $actionResult = static::getExceptionActionResult($request, $t);
        } finally {
            $actionResult->sendHeadersAndRender();
        }
    }

    protected static function getExceptionActionResult(Request $request, Throwable $t): ViewAbstract {
        $exceptionResult = new JsonView(['error' => 'Server error :(']);
        $exceptionResult->setResponseCode(500);

        if ($t instanceof RoutingException || $t instanceof LogicException) {
            $exceptionResult = new JsonView(['error' => 'Unprocessable request']);
            $exceptionResult->setResponseCode(400);
        }

        return $exceptionResult;
    }

    private static function getCurrentControllerClassName(Request $request): ?string {
        if (!$request->feature || !$request->controller) {
            Super::log()->warning('Empty URI parts');
            return null;
        }

        if (!AvailableFeaturesEnum::isValid($request->feature)) {
            Super::log()->warning("Invalid feature '{$request->feature}'");
            return null;
        }

        if (empty(FeatureControllerMap::getMap()[$request->feature])) {
            Super::log()->warning("Feature '{$request->feature}' not in controller map");
            return null;
        }

        foreach (FeatureControllerMap::getMap()[$request->feature] as $fullControllerClassName) {
            if (Utils::getClassSimpleName($fullControllerClassName) === $request->controller) {
                return $fullControllerClassName;
            }
        }

        return null;
    }
}
