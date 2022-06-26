<?php

namespace AsuraNetwork\factions;

use AsuraNetwork\factions\event\FactionCreateEvent;
use AsuraNetwork\factions\utils\FactionData;
use AsuraNetwork\Loader;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

class FactionsFactory{
    use SingletonTrait;

    /** @var Faction[] */
    private array $factions = [];

    public function init(): void{
        if (!is_dir(Loader::getInstance()->getDataFolder() . "factions")) @mkdir(Loader::getInstance()->getDataFolder() . "factions");
        foreach (glob(Loader::getInstance()->getDataFolder() . "factions/"."*.yml") as $file) {
            $this->add(new Faction(new FactionData(yaml_parse_file($file))));
        }
        Loader::getInstance()->getLogger()->info(TextFormat::YELLOW . "All factions have been loaded, number of factions loaded: " . count($this->getFactions()));
    }

    public function add(Faction $faction): bool{
        if ($this->exists($faction->getName())) return false;
        $this->factions[$faction->getName()] = $faction;
        return true;
    }

    public function exists(string $name): bool{
        return isset($this->factions[$name]);
    }

    public function get(string $name): ?Faction{
        return $this->factions[$name] ?? null;
    }

    public function create(array $data): void{
        if ($this->exists($data['name'])){
            return;
        }
        if ($this->add($faction = new Faction(new FactionData($data)))) {
            (new FactionCreateEvent($faction))->call();
        }
    }

    public function delete(string $name): void{
        $this->get($name)?->delete();
        unset($this->factions[$name]);
    }


    /**
     * @return Faction[]
     */
    public function getFactions(): array{
        return $this->factions;
    }
}