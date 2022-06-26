<?php

namespace AsuraNetwork;

use AsuraNetwork\economy\EconomyFactory;
use AsuraNetwork\factions\FactionsFactory;
use AsuraNetwork\factions\listener\FactionListener;
use AsuraNetwork\session\listener\SessionListener;
use AsuraNetwork\session\SessionFactory;
use AsuraNetwork\utils\ConfigUtils;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase{
    use SingletonTrait;
    
    public static mixed $factionConfig = false;
    public static mixed $cooldownsConfig = false;
    public static mixed $config = false;

    public function onEnable(): void{
        $this->saveConfig();
        $this->saveResource("abilities.yml");
        $this->saveResource("cooldowns.yml");
        $this->saveResource("faction.yml");

        self::$config = ConfigUtils::load($this->getDataFolder() . "config.yml");
        self::$factionConfig = ConfigUtils::load($this->getDataFolder() . "faction.yml");
        self::$cooldownsConfig = ConfigUtils::load($this->getDataFolder() . "cooldowns.yml");

        if(!date_default_timezone_set(self::$config["time-zone"] ??"America/Chicago")){
            $this->getLogger()->error(TextFormat::RED . "The timezone identifier isn't valid");
        } else {
            $this->getLogger()->info(TextFormat::GREEN . "The time zone has been set to " . self::$config["time-zone"] ??"America/Chicago");
        }

        FactionsFactory::getInstance()->init();
        SessionFactory::getInstance()->init();
        EconomyFactory::getInstance()->init();

        $this->initListeners();
    }

    private function initListeners(): void{
        foreach ([new SessionListener(), new FactionListener()] as $listener) {
            $this->getServer()->getPluginManager()->registerEvents($listener, $this);
        }
    }
}