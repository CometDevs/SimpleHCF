<?php

namespace AsuraNetwork\modules\scoreboard;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

final class ScoreboardModule{
    use SingletonTrait;

    private array $players = [];

    public function add(Player $player): self{
        $title = "HCF Core v3.0";
        $player->getNetworkSession()->sendDataPacket(SetDisplayObjectivePacket::create("sidebar", $player->getName(), $title, "dummy", 1));
        $this->players[$player->getName()] = $player->getName();
        return $this;
    }

    public function has(Player $player): bool{
        return isset($this->players[$player->getName()]);
    }

    public function close(Player $player): void{
        if(isset($this->players[$player->getName()])){
            $player->getNetworkSession()->sendDataPacket(RemoveObjectivePacket::create($player->getName()));
            unset($this->players[$player->getName()]);
        }
    }

    public function setLine(Player $player, int $score, string $message): self{
        if(!isset($this->scoreboards[$player->getName()])){
            $this->add($player);
            $this->setLine($player, $score, $message);
            return $this;
        }
        if($score > 15 || $score < 1){
            return $this;
        }
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $player->getName();
        $entry->type = $entry::TYPE_FAKE_PLAYER;
        $entry->customName = $message;
        $entry->score = $score;
        $entry->scoreboardId = $score;
        $player->getNetworkSession()->sendDataPacket(SetScorePacket::create(SetScorePacket::TYPE_CHANGE, [$entry]));
        return $this;
    }
}