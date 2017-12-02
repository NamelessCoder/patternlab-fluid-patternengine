<?php
declare(strict_types=1);
namespace NamelessCoder\FluidPatternEngine\Resolving;

use TYPO3Fluid\Fluid\View\TemplatePaths;

class PatternLabTemplatePaths extends TemplatePaths
{
    public function getPartialPathAndFilename($partialName)
    {
        return parent::getPartialPathAndFilename((new PartialNamingHelper())->determinePatternCleanName($partialName));
    }

    public function getLayoutPathAndFilename($layoutName = 'default')
    {
        $paths = $this->getLayoutRootPaths();
        return $this->resolveFileInPaths($paths, $layoutName);
    }
}
