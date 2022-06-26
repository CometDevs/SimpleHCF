<?php

namespace AsuraNetwork\systems\factions;

use AsuraNetwork\Loader;
use AsuraNetwork\systems\factions\event\FactionCreateEvent;
use AsuraNetwork\systems\factions\utils\FactionData;
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

    public function add(Faction $faction): void{
        $this->factions[$faction->getName()] = $faction;
    }

    public function exists(string $name): bool{
        return isset($this->factions[$name]);
    }

    public function create(array $data): void{
        if ($this->exists($data['name'])){
            return;
        }
        $this->add($faction = new Faction(new FactionData($data)));
        (new FactionCreateEvent($faction))->call();
    }


    /**
     * @return Faction[]
     */
    public function getFactions(): array{
        return $this->factions;
    }
}