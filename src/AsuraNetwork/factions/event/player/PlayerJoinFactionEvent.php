<?php

namespace AsuraNetwork\factions\event\player;

use AsuraNetwork\factions\event\FactionEvent;
use AsuraNetwork\factions\Faction;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class PlayerJoinFactionEvent extends FactionEvent implements Cancellable{
    use CancellableTrait;

    protected Player $player;

    public function __construct(Faction $faction, Player $player){
        parent::__construct($faction);
        $this->player = $player;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player{
        return $this->player;
    }

}