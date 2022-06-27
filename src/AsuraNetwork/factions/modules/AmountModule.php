<?php

namespace AsuraNetwork\factions\modules;

use AsuraNetwork\factions\Faction;

class AmountModule{

    protected Faction $faction;
    protected int|float $amount;

    /**
     * @param Faction $faction
     * @param int|float $amount
     */
    public function __construct(Faction $faction, int|float $amount){
        $this->faction = $faction;
        $this->amount = $amount;
    }

    /**
     * @return Faction
     */
    public function getFaction(): Faction{
        return $this->faction;
    }

    public function get(): int|float{
        return $this->amount;
    }

    public function add(int|float $amount): void{
        $this->amount += abs($amount);
    }

    public function reduce(int|float $amount): void{
        $this->amount -= abs($amount);
    }

    public function set(int|float $amount): void{
        $this->amount = abs($amount);
    }

}