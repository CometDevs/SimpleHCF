<?php

namespace AsuraNetwork\factions;

use AsuraNetwork\factions\event\FactionCreateEvent;
use AsuraNetwork\factions\event\FactionDeleteEvent;
use AsuraNetwork\factions\utils\FactionData;
use AsuraNetwork\Loader;
use pocketmine\utils\SingletonTrait;

class FactionsFactory{
    use SingletonTrait;

    /** @var Faction[] */
    private array $factions = [];

    public function __construct(){
        self::$instance = $this;
    }

    private function init(): void{
        foreach (glob(Loader::getInstance()->getDataFolder() . "factions/"."*.yml") as $file) {
            $this->add(new Faction(new FactionData(yaml_parse_file($file))));
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

    public function delete(string $name): void{
    }


    /**
     * @return Faction[]
     */
    public function getFactions(): array{
        return $this->factions;
    }
}