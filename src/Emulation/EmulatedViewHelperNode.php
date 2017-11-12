<?php
namespace NamelessCoder\FluidPatternEngine\Emulation;

use TYPO3Fluid\Fluid\Core\Parser\ParsingState;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class EmulatedViewHelperNode extends ViewHelperNode
{
    public function __construct(RenderingContextInterface $renderingContext, $namespace, $identifier, $arguments, ParsingState $state)
    {
        $resolver = $renderingContext->getViewHelperResolver();
        $this->arguments = $arguments;
        $this->viewHelperClassName = $resolver->resolveViewHelperClassName($namespace, $identifier);
        $this->uninitializedViewHelper = $resolver->createViewHelperInstanceFromClassName($this->viewHelperClassName);
        $this->uninitializedViewHelper->setViewHelperNode($this);
        // Note: RenderingContext required here though replaced later. See https://github.com/TYPO3Fluid/Fluid/pull/93
        $this->uninitializedViewHelper->setRenderingContext($renderingContext);
        $this->argumentDefinitions = $resolver->getArgumentDefinitionsForViewHelper($this->uninitializedViewHelper);
        $this->rewriteBooleanNodesInArgumentsObjectTree($this->argumentDefinitions, $this->arguments);
        if (!is_a($this->viewHelperClassName, EmulatingViewHelper::class, true)) {
            $this->validateArguments($this->argumentDefinitions, $arguments);
        } else {
            $this->uninitializedViewHelper->setOriginalClassName($this->viewHelperClassName);
        }
    }
}
