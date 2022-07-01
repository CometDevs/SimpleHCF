<?php

namespace AsuraNetwork\factions\command\subcommands\leader;

use AsuraNetwork\factions\command\arguments\PlayerArgument;
use AsuraNetwork\factions\command\constraints\RequiredRoleConstraint;
use AsuraNetwork\factions\utils\FactionRole;
use AsuraNetwork\language\LanguageFactory;
use AsuraNetwork\session\SessionFactory;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class InviteSubCommand extends BaseSubCommand{

    protected function prepare(): void{
        $this->addConstraint(new RequiredRoleConstraint($this, [FactionRole::COLEADER(), FactionRole::LEADER()]));
        $this->registerArgument(0, new PlayerArgument("target"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        if (!$sender instanceof Player) return;
        $player = $args['target'];
        $s_session = SessionFactory::getInstance()->get($sender->getName());
        if ($player instanceof Player){
            $p_session = SessionFactory::getInstance()->get($player->getName());
            if($p_session->getInvitesModule()->has($s_session->getFaction()?->getSimplyName())){
                $s_session->sendTranslation('player-already-invited');
                return;
            }
            $p_session->getInvitesModule()->add($s_session->getFaction(), $sender->getName());
            $p_session->sendTranslation('invited-by', [$sender->getName(), $s_session->getFaction()?->getSimplyName()]);
        }
    }
}