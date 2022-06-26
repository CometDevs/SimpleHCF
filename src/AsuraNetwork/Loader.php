<?php

namespace AsuraNetwork;

use AsuraNetwork\economy\EconomyFactory;
use AsuraNetwork\factions\FactionsFactory;
use AsuraNetwork\session\SessionFactory;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase{

    /** @var FactionsFactory */
    private static FactionsFactory $factionsFactory;
    /** @var SessionFactory */
    private static SessionFactory $sessionsFactory;

    public function onEnable(): void{
        self::$factionsFactory = new FactionsFactory();
        self::$sessionsFactory = new SessionFactory();

        // Please not use method static with main, use singletontrait
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