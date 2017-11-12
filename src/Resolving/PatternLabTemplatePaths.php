<?php
namespace NamelessCoder\FluidPatternEngine\Resolving;

use TYPO3Fluid\Fluid\View\TemplatePaths;

class PatternLabTemplatePaths extends TemplatePaths
{
    public function getPartialPathAndFilename($partialName)
    {
        $pattern = implode('.+', explode('-', $partialName));
        $allAvailablePartials = $this->resolveAvailablePartialFiles($this->getFormat());
        foreach ($allAvailablePartials as $availablePartialPathAndFilename) {
            if (preg_match('/' . $pattern . '/i', $availablePartialPathAndFilename)) {
                return $availablePartialPathAndFilename;
            }
        }
        return parent::getPartialPathAndFilename($partialName);
    }

    public function getLayoutPathAndFilename($layoutName = 'Default')
    {
        $paths = $this->getLayoutRootPaths();
        return $this->resolveFileInPaths($paths, $layoutName);
    }
}
