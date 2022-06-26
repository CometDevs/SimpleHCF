<?php

namespace AsuraNetwork;

use AsuraNetwork\economy\EconomyFactory;
use AsuraNetwork\factions\FactionsFactory;
use AsuraNetwork\session\SessionFactory;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Loader extends PluginBase{
    use SingletonTrait;

    public function onEnable(): void{
        FactionsFactory::getInstance()->init();
        SessionFactory::getInstance()->init();
        // Please don't use method static with main, use SingletonTrait
        EconomyFactory::getInstance()->init();
    }
}