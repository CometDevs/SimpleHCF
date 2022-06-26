<?php

namespace AsuraNetwork\systems\factions;

use AsuraNetwork\Loader;
use pocketmine\utils\SingletonTrait;

class FactionsManager{
    use SingletonTrait;

    /** @var Faction[] */
    private array $factions = [];

    public function __construct(){
        self::$instance = $this;
    }

    private function init(): void{
        foreach (glob(Loader::getInstance()->getDataFolder() . "factions/*.yml") as $file) {
            $contents = yaml_parse_file($file);
        }
    }

    /**
     * @return Faction[]
     */
    public function getFactions(): array{
        return $this->factions;
    }
}