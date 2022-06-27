<?php

namespace AsuraNetwork\session\modules;

use AsuraNetwork\session\Session;

class StatsModule extends Module{

    protected int $kills;
    protected int $deaths;

    /**
     * @param Session $session
     * @param int $kills
     * @param int $deaths
     */
    public function __construct(Session $session, int $kills = 0, int $deaths = 0){
        parent::__construct($session);
        $this->kills = $kills;
        $this->deaths = $deaths;
    }

    /**
     * @return int
     */
    public function getDeaths(): int{
        return $this->deaths;
    }

    /**
     * @return int
     */
    public function getKills(): int{
        return $this->kills;
    }

    public function getId(): string{
        return ModuleIds::STATS;
    }
}