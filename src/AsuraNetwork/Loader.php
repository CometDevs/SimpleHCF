<?php

namespace AsuraNetwork;

use AsuraNetwork\economy\EconomyFactory;
use AsuraNetwork\factions\FactionsFactory;
use AsuraNetwork\session\SessionFactory;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Loader extends PluginBase{
    use SingletonTrait;

    /** @var FactionsFactory */
    private static FactionsFactory $factionsFactory;
    /** @var SessionFactory */
    private static SessionFactory $sessionsFactory;

    public function onEnable(): void{
        self::$factionsFactory = new FactionsFactory();
        self::$sessionsFactory = new SessionFactory();

        // Please don't use method static with main, use SingletonTrait
        EconomyFactory::getInstance()->init();
    }

    /**
     * @return FactionsFactory
     */
    public static function getFactionsFactory(): FactionsFactory{
        return self::$factionsFactory;
    }

    /**
     * @return SessionFactory
     */
    public static function getSessionsFactory(): SessionFactory{
        return self::$sessionsFactory;
    }
}