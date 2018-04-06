<?php
declare(strict_types=1);
namespace NamelessCoder\FluidPatternEngine\Resolving;

use PatternLab\Config;
use PatternLab\PatternData;

class PartialNamingHelper
{
    public function determinePartialNameForPattern(string $patternName): string
    {
        $patternName = $this->determinePatternCleanName($patternName);
        $parts = array_map('ucfirst', explode('-', $patternName));
        return ucfirst(array_shift($parts)) . DIRECTORY_SEPARATOR . implode('', array_map('ucfirst', $parts));
    }

    public function determineTargetFileLocationForPattern(string $patternName): string
    {
        $originalPatternName = $patternName;
        $patternName = $this->determinePatternCleanName($patternName);
        $directory = realpath(Config::getOption('fluidTYPO3ExtensionExportPath'));
        $parts = array_map('ucfirst', explode('-', $patternName));
        $type = array_shift($parts);
        switch ($type) {
            case 'Templates':
                return $directory . DIRECTORY_SEPARATOR . 'Resources/Private/Templates/Default/' . implode('/', $parts) . '.html';
                break;
            case 'Pages':
                return $directory . DIRECTORY_SEPARATOR . 'Resources/Private/Templates/Page/' . implode('/', $parts) . '.html';
                break;
            default:
                return $directory . DIRECTORY_SEPARATOR . 'Resources/Private/Partials/' . $this->determinePatternSubPath($patternName) . '.html';
                break;
        }
    }

    public function determinePatternSubPath(string $patternName): string
    {
        $configuration = PatternData::get();
        foreach ($configuration as $patternConfiguration) {
            if ($patternConfiguration['category'] === 'pattern') {
                if (
                    $patternConfiguration['name'] === $patternName
                    || $patternConfiguration['path'] . DIRECTORY_SEPARATOR . $patternConfiguration['name'] === $patternName
                    || $patternConfiguration['nameDash'] === $patternName
                    || $patternConfiguration['nameClean'] === $patternName
                    || $patternConfiguration['partial'] === $patternName
                ) {
                    return implode(DIRECTORY_SEPARATOR, array_map('ucfirst', array_values($patternConfiguration['breadcrumb']))) . DIRECTORY_SEPARATOR . implode('', array_map('ucfirst', explode('-', $patternConfiguration['nameDash'])));
                }
            }
        }
        return $patternName;
    }
    public function determinePatternCleanName(string $patternName): string
    {
        $configuration = PatternData::get();
        foreach ($configuration as $patternConfiguration) {
            if ($patternConfiguration['category'] === 'pattern') {
                if (
                    $patternConfiguration['name'] === $patternName
                    || $patternConfiguration['path'] === $patternName
                    || $patternConfiguration['nameDash'] === $patternName
                    || $patternConfiguration['nameClean'] === $patternName
                    || $patternConfiguration['partial'] === $patternName
                ) {
                    return $patternConfiguration['partial'];
                }
            }
        }
        return $patternName;
    }
}
