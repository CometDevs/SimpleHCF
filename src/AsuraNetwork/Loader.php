<?php

namespace AsuraNetwork;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Loader extends PluginBase{
    use SingletonTrait;

    public function onLoad(): void{
        self::$instance = $this;
    }

    
}