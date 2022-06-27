<?php
namespace AsuraNetwork\koth\command;

use AsuraNetwork\koth\command\subcommands\HelpSubCommand;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
* Class KothCommand
* @package AsuraNetwork\koth\command
*/
class KothCommand extends BaseCommand {

  /**
  * @return void
  */
  protected function prepare(): void {
    $this->setUsage(TextFormat::GREEN . "/koth help");
    $this->registerSubCommand(new HelpSubCommand("help", "Koth commands."));
  }

  /**
  * @param CommandSender $sender
  * @param string $aliasUsed
  * @param array $args
  * @return void
  */
  public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
    // Nothing??
  }
}