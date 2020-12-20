<?php declare(strict_types=1);

namespace Surda\Maker\Util;

class ClassNameDetails
{
    /** @var string */
    private $fullClassName;

    /** @var string */
    private $namespacePrefix;

    /** @var string */
    private $suffix;

    /**
     * @param string $fullClassName
     * @param string $namespacePrefix
     * @param string $suffix
     */
    public function __construct(string $fullClassName, string $namespacePrefix, string $suffix = '')
    {
        $this->fullClassName = $fullClassName;
        $this->namespacePrefix = $namespacePrefix;
        $this->suffix = $suffix;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullClassName;
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return Str::getShortClassName($this->fullClassName);
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return Str::getNamespace($this->fullClassName);
    }

    /**
     * Returns the original class name the user entered (after
     * being cleaned up).
     *
     * For example, assuming the namespace is App\Entity:
     *      App\Entity\Admin\User => Admin\User
     *
     * @return string
     */
    public function getRelativeName(): string
    {
        return str_replace($this->namespacePrefix . '\\', '', $this->fullClassName);
    }

    /**
     * @return string
     */
    public function getRelativeNameWithoutSuffix(): string
    {
        return Str::removeSuffix($this->getRelativeName(), $this->suffix);
    }
}