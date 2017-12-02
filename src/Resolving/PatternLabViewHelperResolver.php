<?php
declare(strict_types=1);
namespace NamelessCoder\FluidPatternEngine\Resolving;

use NamelessCoder\FluidPatternEngine\Emulation\EmulatingViewHelper;
use NamelessCoder\FluidPatternEngine\Hooks\HookManager;
use PatternLab\Config;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolver;

class PatternLabViewHelperResolver extends ViewHelperResolver
{
    public function resolveViewHelperClassName($namespaceIdentifier, $methodIdentifier)
    {
        foreach (HookManager::getHookSubscriberInstances() as $hookSubscriberInstance) {
            $hookResolvedClass = $hookSubscriberInstance->resolveViewHelperClassName($namespaceIdentifier, $methodIdentifier);
            if ($hookResolvedClass) {
                return $hookResolvedClass;
            }
        }

        if ($namespaceIdentifier === 'plio') {
            $possibleFilename = Config::getOption('sourceDir') . DIRECTORY_SEPARATOR . '_viewhelpers' . DIRECTORY_SEPARATOR . ucfirst(str_replace('.', DIRECTORY_SEPARATOR, $methodIdentifier)) . 'ViewHelper.php';
            if (file_exists($possibleFilename)) {
                (function() use ($possibleFilename) {
                    require_once $possibleFilename;
                })();
                $expectedClassName = '\\' . ucfirst(str_replace(DIRECTORY_SEPARATOR, '\\', $methodIdentifier)) . 'ViewHelper';
                $classFileContents = file_get_contents($possibleFilename);
                $namespacePosition = strpos($classFileContents, PHP_EOL . 'namespace ');
                if ($namespacePosition) {
                    $namespace = substr($classFileContents, $namespacePosition + 11, strpos($classFileContents, ';', $namespacePosition) - $namespacePosition - 11);
                    $expectedClassName = $namespace . $expectedClassName;
                }
                if (class_exists($expectedClassName)) {
                    return $expectedClassName;
                }
            }
            throw new Exception('ViewHelper ' . $namespaceIdentifier . ':' . $methodIdentifier . ' could not be resolved');
        }

        try {
            return parent::resolveViewHelperClassName($namespaceIdentifier, $methodIdentifier);
        } catch (\TYPO3Fluid\Fluid\Exception $exception) {
            if (isset($this->namespaces[$namespaceIdentifier])) {
                $className = end($this->namespaces[$namespaceIdentifier]) . '\\' . implode('\\', array_map('ucfirst', explode('.', $methodIdentifier))) . 'ViewHelper';
                class_alias(EmulatingViewHelper::class, $className);
                return $className;
            }
        }

        return parent::resolveViewHelperClassName($namespaceIdentifier, $methodIdentifier);
    }

    public function isNamespaceValid($namespaceIdentifier)
    {
        return isset($this->namespaces[$namespaceIdentifier]);
    }
}
