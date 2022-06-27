<?php

namespace AsuraNetwork\factions\modules;

use AsuraNetwork\factions\utils\FactionConfig;

trait ModulesTrait{

    public function initModules(): void{
        $this->modules[self::BALANCE_MODULE] = new AmountModule($this, $this->factionData->getSimple(self::BALANCE_MODULE, FactionConfig::getStartBalance()));
        $this->modules[self::DTR_MODULE] = new AmountModule($this, $this->factionData->getSimple(self::DTR_MODULE, 1.1));
        $this->modules[self::POINTS_MODULE] = new AmountModule($this, $this->factionData->getSimple(self::POINTS_MODULE, 0));
        $this->modules[self::KILLS_MODULE] = new AmountModule($this, $this->factionData->getSimple(self::KILLS_MODULE, 0));
        $this->modules[self::KOTH_CAPPED_MODULE] = new AmountModule($this, $this->factionData->getSimple(self::KOTH_CAPPED_MODULE, 0));
    }

    public function getBalance(): int{
        return intval($this->modules[self::BALANCE_MODULE]->get());
    }

    public function getDTR(): int{
        return floatval($this->modules[self::DTR_MODULE]->get());
    }

    public function getPoints(): int{
        return intval($this->modules[self::POINTS_MODULE]->get());
    }

    public function getKills(): int{
        return intval($this->modules[self::KILLS_MODULE]->get());
    }

    public function getKOTHCapped(): int{
        return intval($this->modules[self::KOTH_CAPPED_MODULE]->get());
    }

    public function addKills(int $amount = 1): void{
        $this->modules[self::KILLS_MODULE]->add($amount);
    }

    public function addBalance(int $amount): void{
        $this->modules[self::BALANCE_MODULE]->add($amount);
    }

    public function addDTR(float $amount): void{
        $this->modules[self::DTR_MODULE]->add($amount);
    }

    public function addPoints(int $amount): void{
        $this->modules[self::POINTS_MODULE]->add($amount);
    }

    public function addKOTHCapped(int $amount): void{
        $this->modules[self::KOTH_CAPPED_MODULE]->add($amount);
    }

    public function reduceKills(int $amount = 1): void{
        $this->modules[self::KILLS_MODULE]->reduce($amount);
    }

    public function reduceBalance(int $amount): void{
        $this->modules[self::BALANCE_MODULE]->reduce($amount);
    }

    public function reduceDTR(float $amount): void{
        $this->modules[self::DTR_MODULE]->reduce($amount);
    }

    public function reducePoints(int $amount): void{
        $this->modules[self::POINTS_MODULE]->reduce($amount);
    }

    public function reduceKOTHCapped(int $amount): void{
        $this->modules[self::KOTH_CAPPED_MODULE]->reduce($amount);
    }

    public function setKills(int $amount = 1): void{
        $this->modules[self::KILLS_MODULE]->set($amount);
    }

    public function setBalance(int $amount): void{
        $this->modules[self::BALANCE_MODULE]->set($amount);
    }

    public function setDTR(float $amount): void{
        $this->modules[self::DTR_MODULE]->set($amount);
    }

    public function setPoints(int $amount): void{
        $this->modules[self::POINTS_MODULE]->set($amount);
    }

    public function setKOTHCapped(int $amount): void{
        $this->modules[self::KOTH_CAPPED_MODULE]->set($amount);
    }

}