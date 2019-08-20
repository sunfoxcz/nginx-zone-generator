<?php declare(strict_types=1);

namespace Sunfox\NginxZoneGenerator;

use Nette\Configurator;

final class Bootstrap
{
    public static function boot(): Configurator
    {
        $configurator = new Configurator;
        $configurator->setDebugMode(TRUE);
        $configurator->enableTracy(__DIR__ . '/../log');
        $configurator->setTimeZone('Europe/Prague');
        $configurator->setTempDirectory(__DIR__ . '/../temp');
        $configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();
        $configurator->addConfig(__DIR__ . '/../config/config.neon');
        $configurator->addConfig(__DIR__ . '/../config/config.local.neon');
        return $configurator;
    }
}
