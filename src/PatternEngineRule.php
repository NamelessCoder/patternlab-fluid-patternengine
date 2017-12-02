<?php
declare(strict_types=1);
namespace NamelessCoder\FluidPatternEngine;

use \PatternLab\PatternEngine\Rule;

class PatternEngineRule extends Rule
{
    const OPTION_EMULATED_VIEW_HELPERS = 'emulatedViewHelpers';
    const OPTION_NAMESPACES = 'fluidNamespaces';
    const OPTION_TAG_NAME = 'tagName';
    const OPTION_OUTPUTS_TAG_CONTENT = 'outputsTagContent';
    const OPTION_FORCE_CLOSING_TAG = 'forceClosingTag';
    const OPTION_ATTRIBUTE_MAP = 'attributeMap';
    const OPTION_PATHS = 'fluidPaths';

    public function __construct() {

        parent::__construct();

        $this->engineProp = "fluid";
        $this->basePath   = "\\NamelessCoder\\FluidPatternEngine";
    }
}
