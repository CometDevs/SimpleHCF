<?php

namespace AsuraNetwork\factions\command\constraints;

use AsuraNetwork\factions\utils\FactionRole;
use AsuraNetwork\session\SessionFactory;
use CortexPE\Commando\constraint\BaseConstraint;
use CortexPE\Commando\IRunnable;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class RequiredRoleConstraint extends BaseConstraint {
    /** @var FactionRole[] */
    private array $roles = [];

    /**
     * @param IRunnable $context
     * @param FactionRole[] $role
     */
    public function __construct(IRunnable $context, array $role) {
        $this->roles[] = $role;
        parent::__construct($context);
    }

    public function test(CommandSender $sender, string $aliasUsed, array $args): bool {
        if(!$sender instanceof Player) {
            return false;
        } else {
            $session = SessionFactory::getInstance()->get($sender->getName());
            if($session === null) return false;
            $faction = $session->getFaction();
            return $this->check($faction?->getMember($sender->getName())?->getFactionRole());
        }
    }

    private function check(?FactionRole $role): bool{
        if ($role === null) return false;
        foreach ($this->roles as $roleTo) {
            if ($role->equals($roleTo)){
                return true;
            }
        }
        return false;
    }

    public function onFailure(CommandSender $sender, string $aliasUsed, array $args): void {
        $sender->sendMessage(TextFormat::RED . "You do not meet the required privileges to run this command.");
    }

    public function isVisibleTo(CommandSender $sender): bool {
        return $this->test($sender, "", []);
    }
}