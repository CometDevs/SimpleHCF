<?php

namespace AsuraNetwork\economy;

use AsuraNetwork\economy\providers\BedrockEconomyProvider;
use AsuraNetwork\economy\providers\CapitalProvider;
use AsuraNetwork\economy\providers\EconomyAPIPProvider;
use AsuraNetwork\economy\providers\EconomyProvider;
use AsuraNetwork\Loader;
use pocketmine\utils\SingletonTrait;

/**
 * @link https://github.com/DaPigGuy/libPiggyEconomy
 */
class EconomyFactory{
    use SingletonTrait;

    /** @var EconomyProvider[] */
    private array $providers = [];
    private string $provider = "bedrockeconomy";

    public function __construct(){
        self::setInstance($this);
        $this->init();
    }

    public function init(): void{
        $this->registerProvider(["bedrockeconomy"], new BedrockEconomyProvider());
        $this->registerProvider(["capital"], new CapitalProvider());
        $this->registerProvider(["economys", "economyapi"], new EconomyAPIPProvider());
        $this->provider = strtolower(Loader::getInstance()->getConfig()->get("economy-provider", "bedrockeconomy"));
    }

    private function registerProvider(array $providerNames, EconomyProvider $provider): void{
        foreach ($providerNames as $providerName) {
            if (isset($this->providers[strtolower($providerName)])) continue;
            $this->providers[strtolower($providerName)] = $provider;
        }
    }

    public function getProvider(): EconomyProvider{
        return $this->providers[strtolower($this->provider)];
    }
}