<?php declare(strict_types=1);

namespace Surda\Maker\Util;

use Nette\Utils\Strings;

class Str
{
    /**
     * Looks for suffixes in strings in a case-insensitive way.
     *
     * @param string $value
     * @param string $suffix
     * @return bool
     */
    public static function hasSuffix(string $value, string $suffix): bool
    {
        return 0 === strcasecmp($suffix, substr($value, -\strlen($suffix)));
    }

    /**
     * Ensures that the given string ends with the given suffix. If the string
     * already contains the suffix, it's not added twice. It's case-insensitive
     * (e.g. value: 'Foocommand' suffix: 'Command' -> result: 'FooCommand').
     *
     * @param string $value
     * @param string $suffix
     * @return string
     */
    public static function addSuffix(string $value, string $suffix): string
    {
        return $value . $suffix;
    }

    /**
     * Ensures that the given string doesn't end with the given suffix. If the
     * string contains the suffix multiple times, only the last one is removed.
     * It's case-insensitive (e.g. value: 'Foocommand' suffix: 'Command' -> result: 'Foo'.
     *
     * @param string $value
     * @param string $suffix
     * @return string
     */
    public static function removeSuffix(string $value, string $suffix): string
    {
        return self::hasSuffix($value, $suffix) ? substr($value, 0, -\strlen($suffix)) : $value;
    }

    /**
     * Transforms the given string into the format commonly used by PHP classes,
     * (e.g. `app:do_this-and_that` -> `AppDoThisAndThat`) but it doesn't check
     * the validity of the class name.
     *
     * @param string $value
     * @param string $suffix
     * @return string
     */
    public static function asClassName(string $value, string $suffix = ''): string
    {
        $value = trim($value);
        $value = str_replace(['-', '_', '.', ':'], ' ', $value);
        $value = ucwords($value);
        $value = str_replace(' ', '', $value);
//        $value = Strings::firstUpper($value);
        $value = ucwords($value);
        $value = self::addSuffix($value, $suffix);

        return $value;
    }

    /**
     * @param string $fullClassName
     * @return string
     */
    public static function getNamespace(string $fullClassName): string
    {
        return substr($fullClassName, 0, (int) strrpos($fullClassName, '\\'));
    }

    /**
     * @param string $value
     * @return string
     */
    public static function normalizeClassName(string $value): string
    {
        return implode('\\', array_map(function ($item) {
            return ucwords($item);
//            return Strings::firstUpper($item);
        }, explode('\\', $value)));
    }

    /**
     * @param string $fullClassName
     * @return string
     */
    public static function getShortClassName(string $fullClassName): string
    {
        if (self::getNamespace($fullClassName) === '') {
            return $fullClassName;
        }

        return substr($fullClassName, (int) strrpos($fullClassName, '\\') + 1);
    }
}