<?php
namespace NamelessCoder\FluidPatternEngine\Loaders;

use NamelessCoder\FluidPatternEngine\Traits\FluidLoader;
use \PatternLab\PatternEngine\Loader;
use TYPO3Fluid\Fluid\Exception;

class PatternLoader extends Loader
{
    use FluidLoader;

    public function render(array $options = [])
    {
        $this->view->assignMultiple($options['data']);
        $this->view->getTemplatePaths()->setTemplateSource($options['pattern']);
        try {
            return $this->view->render();
        } catch (Exception $error) {
            return $error->getMessage() . ' (' . $error->getCode() . ')';
        }
    }
}
