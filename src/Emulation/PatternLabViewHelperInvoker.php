<?php
namespace NamelessCoder\FluidPatternEngine\Emulation;

use NamelessCoder\FluidPatternEngine\PatternEngineRule;
use PatternLab\Config;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInvoker;

class PatternLabViewHelperInvoker extends ViewHelperInvoker
{
    public function invoke(
        $viewHelperClassNameOrInstance,
        array $arguments,
        RenderingContextInterface $renderingContext,
        \Closure $renderChildrenClosure = null
    ) {
        if (is_a(is_string($viewHelperClassNameOrInstance) ? $viewHelperClassNameOrInstance : get_class($viewHelperClassNameOrInstance), EmulatingViewHelper::class, true)) {
            return $this->emulateViewHelperCall($viewHelperClassNameOrInstance, $arguments, $renderingContext, $renderChildrenClosure);
        }

        try {
            return parent::invoke($viewHelperClassNameOrInstance, $arguments, $renderingContext, $renderChildrenClosure);
        } catch (\Exception $exception) {
            return $this->emulateViewHelperCall($viewHelperClassNameOrInstance, $arguments, $renderingContext, $renderChildrenClosure);
        } catch (\Error $error) {
            return $this->emulateViewHelperCall($viewHelperClassNameOrInstance, $arguments, $renderingContext, $renderChildrenClosure);
        }
    }

    protected function emulateViewHelperCall(
        $viewHelperClassName,
        array $arguments,
        RenderingContextInterface $renderingContext,
        \Closure $renderChildrenClosure = null
    ) {
        $instance = $viewHelperClassName;
        if ($viewHelperClassName instanceof EmulatingViewHelper) {
            $viewHelperClassName = $viewHelperClassName->getOriginalClassName();
        } elseif ($viewHelperClassName instanceof ViewHelperInterface) {
            $viewHelperClassName = get_class($viewHelperClassName);
        }
        $emulations = Config::getOption(PatternEngineRule::OPTION_EMULATED_VIEW_HELPERS) ?? [];
        if (isset($emulations[$viewHelperClassName])) {
            $tagName = $emulations[$viewHelperClassName][PatternEngineRule::OPTION_TAG_NAME] ?? null;
            $outputsTagContent = $emulations[$viewHelperClassName][PatternEngineRule::OPTION_OUTPUTS_TAG_CONTENT] ?? true;
            $content = $outputsTagContent ? ($renderChildrenClosure ? $renderChildrenClosure() : $instance->renderChildren()) : null;
            if ($tagName) {
                $tagBuilder = new TagBuilder($tagName, $content);
                $tagBuilder->forceClosingTag($emulations[$viewHelperClassName][PatternEngineRule::OPTION_FORCE_CLOSING_TAG] ?? true);
                foreach ($arguments as $name => $argument) {
                    $tagBuilder->addAttribute($emulations[$viewHelperClassName][PatternEngineRule::OPTION_ATTRIBUTE_MAP][$name] ?? $name, $argument->evaluate($renderingContext));
                }
                return $tagBuilder->render();
            }
            return $content;
        }
    }
}
