<?php
namespace NamelessCoder\FluidPatternEngine\Emulation;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class EmulatingViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * @var string
     */
    protected $originalClassName = self::class;

    public function getOriginalClassName()
    {
        return $this->originalClassName;
    }

    public function setOriginalClassName($originalClassName)
    {
        $this->originalClassName = $originalClassName;
    }

    public function validateArguments()
    {
    }

    public function handleAdditionalArguments(array $arguments)
    {
    }

    public function validateAdditionalArguments(array $arguments)
    {
    }
}
