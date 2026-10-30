<?php declare(strict_types=1);

namespace App\Library\Framework;

final class Utils {
    public static function getClassSimpleName(string $class): string {
        $parts = explode('\\', $class);
        return end($parts);
    }

    public static function getClassNamespace(string $class): string {
        $parts = explode('\\', $class);
        array_pop($parts);
        return implode('\\', $parts);
    }

    /**
     * 'someWords' -> 'some-words'
     */
    public static function camel2kebab(string $string): string {
        return preg_replace_callback(
            '/[A-Z]/',
            function ($match) {
                return '-' . strtolower($match[0]);
            },
            $string,
        );
    }

    /**
     * 'some-words' -> 'someWords'
     */
    public static function kebab2camel(string $string): string {
        return preg_replace_callback(
            '/-+(.)/',
            function ($match) {
                return strtoupper($match[1]);
            },
            trim($string, '-'),
        );
    }
}
