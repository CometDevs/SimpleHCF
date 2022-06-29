<?php

declare(strict_types=1);

namespace AsuraNetwork\factions;

use AsuraNetwork\factions\command\FactionCommand;
use AsuraNetwork\factions\event\FactionCreateEvent;
use AsuraNetwork\factions\utils\FactionData;
use AsuraNetwork\Loader;
use pocketmine\Server;
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
        Server::getInstance()->getCommandMap()->register("faction", new FactionCommand(Loader::getInstance(), "faction", "Faction command", ['f', 't']));
    }

    public function add(Faction $faction): bool{
        if ($this->exists($faction->getSimplyName())) return false;
        $this->factions[$faction->getSimplyName()] = $faction;
        return true;
    }

    public function exists(string $name): bool{
        return isset($this->factions[$name]);
    }

    public function get(string $name): ?Faction{
        return $this->factions[$name] ?? null;
    }

    public function getFactionByPrefix(string $name): ?Faction{
        $found = null;
        $name = strtolower($name);
        $delta = PHP_INT_MAX;
        foreach($this->getFactions() as $faction){
            if(stripos($faction->getSimplyName(), $name) === 0){
                $curDelta = strlen($faction->getSimplyName()) - strlen($name);
                if($curDelta < $delta){
                    $found = $faction;
                    $delta = $curDelta;
                }
                if($curDelta === 0){
                    break;
                }
            }
        }

        return $found;
    }

    public function create(array $data): ?Faction{
        if ($this->exists($data['name'])){
            return null;
        }
        if ($this->add($faction = new Faction(new FactionData($data)))) {
            $faction->save();
            (new FactionCreateEvent($faction))->call();
        }
        return $faction;
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