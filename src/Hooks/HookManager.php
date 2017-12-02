<?php
declare(strict_types=1);
namespace NamelessCoder\FluidPatternEngine\Hooks;

use PatternLab\Config;

class HookManager
{
    /**
     * @return FluidPatternEngineHookInterface[]
     */
    public static function getHookSubscriberInstances(): array
    {
        static $instances = [];
        if (empty($instances)) {
            $hooks = Config::getOption('fluidHooks');
            foreach ($hooks ?? [] as $hookClass) {
                if (!is_a($hookClass, FluidPatternEngineHookInterface::class, true)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Class "%s" must implement "%s"!',
                            $hookClass,
                            FluidPatternEngineHookInterface::class
                        )
                    );
                }
                $instances[] = new $hookClass();
            }
        }
        return $instances;
    }
}
