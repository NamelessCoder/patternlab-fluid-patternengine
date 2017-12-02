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
        return implode('/', $parts);
    }

    public function determineTargetFileLocationForPattern(string $patternName): string
    {
        $originalPatternName = $patternName;
        $patternName = $this->determinePatternCleanName($patternName);
        $directory = realpath(Config::getOption('fluidTYPO3ExtensionExportPath'));
        $parts = array_map('ucfirst', explode('-', $patternName));
        $type = array_shift($parts);
        switch ($type) {
            case 'Atoms':
            case 'Molecules':
            case 'Organisms':
                return $directory . DIRECTORY_SEPARATOR . 'Resources/Private/Partials//' . $this->determinePartialNameForPattern($patternName) . '.html';
                break;
            case 'Templates':
                return $directory . DIRECTORY_SEPARATOR . 'Resources/Private/Templates/Default/' . implode('/', $parts) . '.html';
                break;
            case 'Pages':
                return $directory . DIRECTORY_SEPARATOR . 'Resources/Private/Templates/Page/' . implode('/', $parts) . '.html';
                break;
            default:
                throw new \RuntimeException(
                    sprintf(
                        'The pattern type "%s" (implied from "%s") is unknown.',
                        $type,
                        $originalPatternName
                    )
                );
                break;
        }
    }

    public function determinePatternCleanName(string $patternName): string
    {
        $configuration = PatternData::get();
        foreach ($configuration as $patternConfiguration) {
            if ($patternConfiguration['category'] === 'pattern') {
                if (
                    $patternConfiguration['name'] === $patternName
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
