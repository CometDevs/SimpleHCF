<?php

namespace AsuraNetwork\modules\claim\utils;

use AsuraNetwork\factions\claim\Claim;
use AsuraNetwork\factions\claim\types\CitadelClaim;
use AsuraNetwork\factions\claim\types\ConquestClaim;
use AsuraNetwork\factions\claim\types\FactionClaim;
use AsuraNetwork\factions\claim\types\GlowstoneClaim;
use AsuraNetwork\factions\claim\types\KOTHClaim;
use AsuraNetwork\factions\claim\types\RoadClaim;
use AsuraNetwork\factions\claim\types\SpawnClaim;
use AsuraNetwork\factions\Faction;

final class ClaimTypes{

    public const FACTION = "faction";
    public const GLOWSTONE = "glowstone";
    public const SPAWN = "spawn";
    public const KOTH = "koth";
    public const CITADEL = "citadel";
    public const ROAD = "road";
    public const CONQUEST = "conquest";

    public static function strToClass(string $claim, Faction $faction, array $positions): Claim{
        return match ($claim) {
            self::GLOWSTONE => new GlowstoneClaim($faction, $positions),
            self::SPAWN => new SpawnClaim($faction, $positions),
            self::KOTH => new KOTHClaim($faction, $positions),
            self::CITADEL => new CitadelClaim($faction, $positions),
            self::ROAD => new RoadClaim($faction, $positions),
            self::CONQUEST => new ConquestClaim($faction, $positions),
            default => new FactionClaim($faction, $positions),
        };
    }

}