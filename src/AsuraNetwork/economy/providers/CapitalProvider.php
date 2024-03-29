<?php

declare(strict_types=1);

namespace AsuraNetwork\economy\providers;

use AsuraNetwork\Loader;
use pocketmine\player\Player;
use pocketmine\Server;
use SOFe\Capital\Capital;
use SOFe\Capital\CapitalException;
use SOFe\Capital\LabelSet;
use SOFe\Capital\Schema\Complete;

/**
 * @link https://github.com/DaPigGuy/libPiggyEconomy/pull/4
 */
class CapitalProvider extends EconomyProvider{

    private string $version = "0.1.0";
    private string $oracle;
    private Complete $selector;

    public static function checkDependencies(): bool{
        return Server::getInstance()->getPluginManager()->getPlugin("Capital") !== null;
    }

    public function __construct(){
        Capital::api($this->version, fn(Capital $api) => $this->selector = $api->completeConfig(Loader::getInstance()->getConfig()->get("capital-selector")));
            $this->oracle = Loader::getInstance()->getName();
    }

    public function getMonetaryUnit(): string{
        return "$";
    }

    public function getMoney(Player $player, callable $callback): void{
        Capital::api($this->version, function (Capital $api) use ($callback, $player) {
            $accounts = yield from $api->findAccountsComplete($player, $this->selector);
            $callback($api->getBalance($accounts[0]));
        });
    }

    public function giveMoney(Player $player, float $amount, ?callable $callback = null): void{
        Capital::api($this->version, function (Capital $api) use ($callback, $amount, $player) {
            try {
                yield from $api->addMoney($this->oracle, $player, $this->selector, (int)$amount, new LabelSet(["reason" => "some reason"]));
                if ($callback) $callback(true);
            } catch (CapitalException $e) {
                if ($callback) $callback(false);
            }
        });
    }

    public function takeMoney(Player $player, float $amount, ?callable $callback = null): void{
        Capital::api($this->version, function (Capital $api) use ($callback, $amount, $player) {
            try {
                yield from $api->takeMoney($this->oracle, $player, $this->selector, (int)$amount, new LabelSet(["reason" => "some reason"]));
                if ($callback) $callback(true);
            } catch (CapitalException $e) {
                if ($callback) $callback(false);
            }
        });
    }

    public function setMoney(Player $player, float $amount, ?callable $callback = null): void{
        $this->getMoney($player, function ($balance) use ($callback, $player, $amount): void {
            $difference = $balance - (int)$amount;
            if ($difference >= 0) {
                $this->takeMoney($player, $difference, fn(?bool $success) => $callback($success));
            } else {
                $this->giveMoney($player, $difference, fn(?bool $success) => $callback($success));
            }
        });
    }
}