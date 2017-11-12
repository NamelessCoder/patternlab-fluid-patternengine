<?php
namespace NamelessCoder\FluidPatternEngine\Emulation;

use TYPO3Fluid\Fluid\Core\Parser\Exception;
use TYPO3Fluid\Fluid\Core\Parser\InterceptorInterface;
use TYPO3Fluid\Fluid\Core\Parser\ParsingState;
use TYPO3Fluid\Fluid\Core\Parser\TemplateParser;

class EmulatingTemplateParser extends TemplateParser
{
    protected function initializeViewHelperAndAddItToStack(ParsingState $state, $namespaceIdentifier, $methodIdentifier, $argumentsObjectTree)
    {
        $viewHelperResolver = $this->renderingContext->getViewHelperResolver();
        if (!$viewHelperResolver->isNamespaceValid($namespaceIdentifier)) {
            return null;
        }
        try {
            $currentViewHelperNode = new EmulatedViewHelperNode(
                $this->renderingContext,
                $namespaceIdentifier,
                $methodIdentifier,
                $argumentsObjectTree,
                $state
            );

            $this->callInterceptor($currentViewHelperNode, InterceptorInterface::INTERCEPT_OPENING_VIEWHELPER, $state);
            $viewHelper = $currentViewHelperNode->getUninitializedViewHelper();
            $viewHelper::postParseEvent($currentViewHelperNode, $argumentsObjectTree, $state->getVariableContainer());
            $state->pushNodeToStack($currentViewHelperNode);
            return $currentViewHelperNode;
        } catch (\TYPO3Fluid\Fluid\Core\ViewHelper\Exception $error) {
            $this->textHandler(
                $state,
                $this->renderingContext->getErrorHandler()->handleViewHelperError($error)
            );
        } catch (Exception $error) {
            $this->textHandler(
                $state,
                $this->renderingContext->getErrorHandler()->handleParserError($error)
            );
        }
    }

}
