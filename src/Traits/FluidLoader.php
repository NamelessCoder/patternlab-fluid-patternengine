<?php
declare(strict_types=1);
namespace NamelessCoder\FluidPatternEngine\Traits;

use NamelessCoder\FluidPatternEngine\Emulation\EmulatingTemplateParser;
use NamelessCoder\FluidPatternEngine\Emulation\PatternLabViewHelperInvoker;
use NamelessCoder\FluidPatternEngine\Hooks\HookManager;
use NamelessCoder\FluidPatternEngine\Resolving\PatternLabTemplatePaths;
use NamelessCoder\FluidPatternEngine\Resolving\PatternLabViewHelperResolver;
use PatternLab\Config;
use TYPO3Fluid\Fluid\View\TemplateView;

trait FluidLoader
{
    /**
     * @var TemplateView
     */
    protected $view;

    /**
     * @var array
     */
    protected $options = [];

    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->view = new TemplateView();
        $templatePaths = new PatternLabTemplatePaths();
        $templatePaths->setFormat(Config::getOption("patternExtension"));
        $templatePaths->setLayoutRootPaths([Config::getOption("styleguideKitPath") . DIRECTORY_SEPARATOR . 'Resources/Private/Layouts/', Config::getOption("sourceDir") . DIRECTORY_SEPARATOR . '_layouts/']);
        $templatePaths->setPartialRootPaths([Config::getOption("styleguideKitPath") . DIRECTORY_SEPARATOR . 'Resources/Private/Partials/', Config::getOption("sourceDir") . DIRECTORY_SEPARATOR . '_patterns/']);
        $templatePaths->setTemplateRootPaths([Config::getOption("styleguideKitPath") . DIRECTORY_SEPARATOR . 'Resources/Private/Templates/', Config::getOption("sourceDir") . DIRECTORY_SEPARATOR . 'Templates/']);
        $this->view->getRenderingContext()->setTemplatePaths($templatePaths);
        $this->view->getRenderingContext()->setTemplateParser(new EmulatingTemplateParser());
        $this->view->getRenderingContext()->setViewHelperInvoker(new PatternLabViewHelperInvoker());
        $this->view->getRenderingContext()->setViewHelperResolver(new PatternLabViewHelperResolver());
        $this->view->getRenderingContext()->getViewHelperResolver()->addNamespace('plio', 'PatternLab\\ViewHelpers');
        foreach (Config::getOption('fluidNamespaces') ?? [] as $namespaceName => $namespaces) {
            $this->view->getRenderingContext()->getViewHelperResolver()->addNamespace($namespaceName, (array) $namespaces);
        }
        foreach (HookManager::getHookSubscriberInstances() as $hookSubscriberInstance) {
            $this->view = $hookSubscriberInstance->viewCreated($this->view);
        }
    }
}
