<?php

namespace AsuraNetwork;

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

    public function onLoad(): void{
        self::$instance = $this;
    }

    public function onEnable(): void{
        self::$factionsFactory = new FactionsFactory();
        self::$sessionsFactory = new SessionFactory();
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