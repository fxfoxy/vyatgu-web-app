<?php declare(strict_types=1);

namespace App\Library\Framework\Routing;

use Error;

class FeatureControllerMapBuilder {
    private const string MAP_CLASS_NAME = "FeatureControllerMap";

    public static function build(): string {
        $controllerCounter = 0;
        $featureCounter    = 0;

        $map = [];
        foreach (AvailableFeaturesEnum::cases() as $feature) {
            $featureCounter++;
            $featureDir       = ROOT_DIR . "/Library/{$feature->value}";
            $featureNamespace = "App\\Library\\{$feature->value}\\";
            $controllers      = glob($featureDir . '/*.php');

            foreach ($controllers as $controllerFilePath) {
                $fullControllerClassName = $featureNamespace . basename($controllerFilePath, '.php');

                if (class_exists($fullControllerClassName)) {
                    $controller = null;
                    try {
                        $controller = new $fullControllerClassName();
                    } catch (Error $e) {
                        // Обход приватных конструкторов
                        if (!str_starts_with($e->getMessage(), 'Call to private')) {
                            throw $e;
                        }
                    }

                    if ($controller instanceof ControllerAbstract) {
                        $controllerCounter++;
                        $map[$feature->value][] = $fullControllerClassName;
                    }
                }
            }
        }

        $mapClassName = self::MAP_CLASS_NAME;

        $php = "<?php declare(strict_types=1);\n"
            . "\n"
            . "namespace App\Library\Framework\Routing\Generated;\n"
            . "\n"
            . "class {$mapClassName} {\n"
            . "    public static function getMap(): array {\n"
            . '        return ' . self::niceVarExport($map, '        ') . ";\n"
            . "    }\n"
            . "}\n";

        file_put_contents(__DIR__ . "/Generated/{$mapClassName}.php", $php);

        return "Found {$featureCounter} features and {$controllerCounter} controllers. Class {$mapClassName} generated.";
    }

    private static function niceVarExport(mixed $value, string $prefixSpaces = ''): string {
        if ($value === null) {
            return 'null';
        }
        if ($value === true) {
            return 'true';
        }
        if ($value === false) {
            return 'false';
        }
        if (!is_array($value)) {
            return var_export($value, true);
        }

        if (count($value) == 0) {
            return '[]';
        }

        $isList = array_is_list($value);

        $maxKeyLength = 0;
        if (!$isList) {
            foreach ($value as $k => $v) {
                $maxKeyLength = max($maxKeyLength, strlen(self::niceVarExport($k)));
            }
        }

        if ($isList && count($value) === 1) {
            return '[' . self::niceVarExport($value[0], $prefixSpaces) . ']';
        }

        $str = "[\n";
        foreach ($value as $k => $v) {
            $str .= $prefixSpaces . '    ';
            if (!$isList) {
                $str .= str_pad(self::niceVarExport($k), $maxKeyLength) . ' => ';
            }
            $str .= self::niceVarExport($v, "{$prefixSpaces}    ") . ",\n";
        }
        return $str . $prefixSpaces . ']';
    }
}
