<?php
declare(strict_types=1);
namespace NamelessCoder\FluidPatternEngine\Hooks;

use TYPO3Fluid\Fluid\View\ViewInterface;

interface FluidPatternEngineHookInterface
{
    public function viewCreated(ViewInterface $view): ViewInterface;

    public function resolveViewHelperClassName(string $namespace, string $methodIdentifier);

    public function validateNamespace(string $namespaceIdentifier): bool;

    public function viewRendered(ViewInterface $view, array $options, string $source, string $rendered): string;
}
