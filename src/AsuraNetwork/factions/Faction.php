<?php

declare(strict_types=1);

namespace AsuraNetwork\factions;

use AsuraNetwork\citadel\CitadelFactory;
use AsuraNetwork\factions\claim\Claim;
use AsuraNetwork\factions\claim\types\CitadelClaim;
use AsuraNetwork\factions\claim\types\ConquestClaim;
use AsuraNetwork\factions\claim\types\GlowstoneClaim;
use AsuraNetwork\factions\claim\types\KOTHClaim;
use AsuraNetwork\factions\claim\types\RoadClaim;
use AsuraNetwork\factions\claim\types\SpawnClaim;
use AsuraNetwork\factions\claim\types\UnraidableClaim;
use AsuraNetwork\factions\event\FactionDeleteEvent;
use AsuraNetwork\factions\event\player\PlayerJoinFactionEvent;
use AsuraNetwork\factions\threads\ThreadManager;
use AsuraNetwork\factions\utils\FactionConfig;
use AsuraNetwork\factions\utils\FactionData;
use AsuraNetwork\factions\utils\FactionMember;
use AsuraNetwork\factions\utils\FactionRole;
use AsuraNetwork\Loader;
use AsuraNetwork\session\Session;
use AsuraNetwork\session\SessionFactory;
use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use RuntimeException;

class Faction{

    public const DTR = "dtr";
    public const KILLS = "kills";
    public const POINTS = "points";
    public const BALANCE = "balance";
    public const KOTH_CAPPED = "koth-capped";
    const GRAY_LINE = "LINE?";

    /** @var FactionMember[] */
    private array $members = [];
    private bool $needsSave = false;
    /** @var Faction[] */
    private array $allies = [];
    private bool $regenerating = false;
    private bool $freezing = false;
    private int $dtrCooldown = 0;
    private ?Location $home = null;
    private ?Claim $claim = null;

    public function __construct(
        private FactionData $factionData){
        $this->initPlayers();
    }

    public function initPlayers(): void{
        foreach ($this->factionData->getData()['members'] as $member => $data) {
            if (FactionRole::fromString($data['role']) == null){
                continue;
            }
            $this->members[$member] = new FactionMember($member, $this, FactionRole::fromString($data['role']), [
                "kills" => $data['kills'],
                "deaths" => $data['deaths'],
                "join-time" => $data['join-time'],
                "invited-by" => $data['invited-by'],
            ]);
        }
    }

    /**
     * @return FactionMember[]
     */
    public function getMembers(): array{
        return $this->members;
    }

    /**
     * @param string $name
     * @return FactionMember|null
     */
    public function getMember(string $name): ?FactionMember{
        return $this->members[$name] ?? null;
    }

    public function addMember(Session $member, string $inviter = "none"): void{
        if ($this->getMember($member->getName()) === null){
            return;
        }
        $ev = new PlayerJoinFactionEvent($this, $member->getPlayerNonNull());
        $ev->call();
        if ($ev->isCancelled()){
            return;
        }
        $this->members[$member->getName()] = new FactionMember($member->getName(), $this, FactionRole::MEMBER(),[
            "kills" => 0,
            "deaths" => 0,
            "join-time" => date('Y-m-d H:i:s'),
            "invited-by" => $inviter
        ]);
        $member->setFaction($this);
        $member->setRole(FactionRole::MEMBER());
        $this->senTranslation("player-joined", [$member->getName(), $inviter]);
        $this->log("Player {$member->getName()} has joined the faction and was invited by $inviter");
    }

    public function playerDeath(string $playerName, string $cause = "desconocido", float $dtrLoss = .99): void{
        $oldDTR = $this->getAmount(self::DTR, 0);
        $newDTR = $this->reduceAmount(self::DTR, $dtrLoss);
        $this->log("$playerName ha muerto y perdio $dtrLoss de DTR, nuevo dtr: $newDTR, causa: $cause");
        $this->sendMessage(TextFormat::RED . "Member Death: " . TextFormat::WHITE . $playerName . TextFormat::EOL . TextFormat::RED . "DTR: " . TextFormat::WHITE . $newDTR);
        if(round($oldDTR, 1) === 0.1 && round($newDTR, 1) <= 0.9) {
            $this->log("faction quedo raid gracias a: $playerName");
        }
        $this->setFreezing(FactionConfig::getFreezeTime());
    }

    /**
     * @return FactionData
     */
    public function getFactionData(): FactionData{
        return $this->factionData;
    }

    public function getSimplyName(): string{
        return $this->getFactionData()->getSimple("name");
    }

    public function getLeader(): FactionMember{
        foreach ($this->members as $member) {
            if ($member->getFactionRole()->equals(FactionRole::LEADER())){
                return $member;
            }
        }
        throw new RuntimeException("Leader not found!");
    }

    public function getAmount(string $amount_type, int|float $default): int|float{
        $default = $amount_type === self::BALANCE ? FactionConfig::getStartBalance() : $default;
        return $this->getFactionData()->getSimple($amount_type, $default);
    }

    public function setAmount(string $amount_type, int|float $amount): int|float{
        $this->log($amount_type . " se estableció a: " . $amount);
        return $this->getFactionData()->getData()[$amount_type] = abs($amount);
    }

    public function addAmount(string $amount_type, int|float $amount, ?string $cause = null): int|float{
        $this->log($amount_type . " se le agrego: " . $amount . " motivo: " . ($cause ?? "desconocido"));
        return $this->getFactionData()->getData()[$amount_type] = ($this->getAmount($amount_type, 0) + abs($amount));
    }

    public function reduceAmount(string $amount_type, int|float $amount, ?string $cause = null): int|float{
        $this->log($amount_type . " se le quitó: " . $amount . " motivo: " . ($cause ?? "desconocido"));
        return $this->getFactionData()->getData()[$amount_type] = ($this->getAmount($amount_type, 0) - abs($amount));
    }

    public function delete(?string $cause = null): void{
        $this->log("Se elimino la faction por motivo: " . ($cause ?? "desconocido"));
        (new FactionDeleteEvent($this))->call();
    }

    public function sendMessage(string $message): void{
        foreach ($this->members as $member) {
            SessionFactory::getInstance()->get($member->getName())?->sendMessage($message);
        }
    }

    public function senTranslation(string $message, array $params = []): void{
        foreach ($this->members as $member) {
            SessionFactory::getInstance()->get($member->getName())?->sendTranslation($message, $params);
        }
    }

    public function getClaim(): ?Claim{
        return $this->claim;
    }

    public function getHome(): ?Location{
        return $this->home;
    }

    public function getName(Player $player): string{
        if ($this->getClaim() instanceof GlowstoneClaim) {
            return TextFormat::GOLD . "Glowstone Mountain (Deathban)";
        } elseif ($this->getClaim() instanceof SpawnClaim) {
            return TextFormat::GREEN . $this->getSimplyName() . " (Safezone)";
        } else if ($this->getClaim() instanceof KOTHClaim) {
            return (TextFormat::AQUA . $this->getSimplyName() . TextFormat::GOLD . " KOTH");
        } else if ($this->getClaim() instanceof CitadelClaim) {
            return (TextFormat::DARK_PURPLE . "Citadel");
        } else if ($this->getClaim() instanceof RoadClaim) {
            return (TextFormat::GOLD . str_replace("Road", " Road", $this->getSimplyName()));
        } else if ($this->getClaim() instanceof ConquestClaim) {
            return (TextFormat::YELLOW . "Conquest");
        } elseif ($this->isMember($player->getName())) {
            return (TextFormat::GREEN . $this->getSimplyName());
        } else if ($this->isAllyPlayer($player->getName())) {
            return (TextFormat::DARK_AQUA . $this->getSimplyName());
        } else {
            return (TextFormat::RED . $this->getSimplyName());
        }
    }

    public function getOnlineMemberAmount(): int{
        $online = 0;
        foreach ($this->members as $member) {
            if ($member->getPlayer()?->isConnected()){
                $online++;
            }
        }
        return $online;
    }

    /**
     * @return FactionMember[]
     */
    public function getOnlineMember(): array{
        $online = [];
        foreach ($this->members as $member) {
            if ($member->getPlayer()?->isConnected()){
                $online[] = $member;
            }
        }
        return $online;
    }

    /**
     * @return Faction[]
     */
    public function getAllies(): array{
        return $this->allies;
    }

    public function flagForSave(): void{
        $this->needsSave = true;
    }

    public function isLeader(string $name): bool{
        return $name === $this->getLeader()->getName();
    }

    public function isMember(string $name): bool{
        foreach ($this->members as $member) {
            if ($member->getName() === $name){
                return true;
            }
        }
        return false;
    }

    public function isAllyPlayer(string $name): bool{
        foreach ($this->allies as $ally) {
            if($ally->isMember($name)){
                return true;
            }
        }
        return false;
    }

    public function isAllyFaction(string $name): bool{
        foreach ($this->allies as $ally) {
            if($ally->getSimplyName() === $name){
                return true;
            }
        }
        return false;
    }

    public function isHasRole(string $name, FactionRole $role): bool{
        $members = array_filter($this->getMembers(), function (FactionMember $member)use($role){
            return $member->getFactionRole()->equals($role);
        });
        foreach ($members as $member) {
            if ($member->getName() == $name){
                return true;
            }
        }
        return false;
    }

    public function sendTeamInfo(Player $player): void{
        // Don't make our null teams have DTR....
        // @HCFactions
        if ($this->getLeader() == null) {
            $player->sendMessage(self::GRAY_LINE);
            $player->sendMessage($this->getName($player));

            if ( $this->getHome() != null && $this->getHome()->getWorld()->getFolderName() != "Overworld") {
                $world = $this->getHome()->getWorld()->getFolderName() == "Nether" ? "Nether" : "End";
                $player->sendMessage(TextFormat::YELLOW . "Location: " . TextFormat::WHITE . ($this->getHome() == null ? "None" : $this->getHome()->getX() . ", " . $this->getHome()->getZ() . " (" . $world . ")"));
            } else {
                $player->sendMessage(TextFormat::YELLOW . "Location: " . TextFormat::WHITE . ($this->getHome() == null ? "None" : $this->getHome()->getX() . ", " . $this->getHome()->getZ()));
            }

            if ($this->getClaim() instanceof CitadelClaim) {
                $cappers = CitadelFactory::getInstance()->getCappers();
                $capperNames = [];

                foreach ($cappers as $capper) {
                    $capperTeam = SessionFactory::getInstance()->get($capper)?->getFaction();

                    if ($capperTeam != null) {
                        $capperNames[] = $capperTeam->getSimplyName();
                    }
                }

                if (!empty($capperNames)) {
                    $player->sendMessage(TextFormat::YELLOW . "Currently captured by: " . TextFormat::RED . join(", ", $capperNames));
                }
            }

            $player->sendMessage(self::GRAY_LINE);
            return;
        }

        $allies = [];
        $coleaders = [];
        $members = [];
        $captains = [];

        foreach ($this->getAllies() as $ally) {
            if ($ally != null) {
                $allies[] = TextFormat::GREEN . "[". TextFormat::YELLOW . $ally->getName($player) . TextFormat::GREEN . "]";
            }
        }


        foreach($this->getMembers() as $member) {
            if ($this->isLeader($member->getName())) {
                continue;
            }

            if($this->isHasRole($member->getName(), FactionRole::COLEADER())) {
                $coleaders[] = $member->isOnline() ? TextFormat::colorize("&a{$member->getName()}[&6{$member->getKills()}]") : TextFormat::colorize("&7{$member->getName()}[&6{$member->getKills()}]");
            } else if($this->isHasRole($member->getName(), FactionRole::CAPTAIN())) {
                $captains[] = $member->isOnline() ? TextFormat::colorize("&a{$member->getName()}[&6{$member->getKills()}]") : TextFormat::colorize("&7{$member->getName()}[&6{$member->getKills()}]");
            } else {
                $members[] = $member->isOnline() ? TextFormat::colorize("&a{$member->getName()}[&6{$member->getKills()}]") : TextFormat::colorize("&7{$member->getName()}[&6{$member->getKills()}]");
            }
        }

        // Now we can actually send all that info we just processed.
        $player->sendMessage(self::GRAY_LINE);

        $teamLine = [];

        $teamLine[] = TextFormat::GRAY . " [" . $this->getOnlineMemberAmount() . "/" . count($this->getMembers()) . "]" . TextFormat::DARK_AQUA . " - " . $this->getName($player);
        $teamLine[] = TextFormat::YELLOW . "HQ: " . TextFormat::WHITE . ($this->getHome() == null ? "None" : $this->getHome()->getX() .", " . $this->getHome()->getZ());

        $player->sendMessage(join("\n", $teamLine));

        if (count($allies) > 0) {
            $player->sendMessage(TextFormat::YELLOW . "Allies: " . implode(", ", $allies));
        }

        $player->sendMessage(TextFormat::YELLOW . "Leader: "  . ($this->getLeader()->isOnline() ? TextFormat::colorize("&a{$this->getLeader()->getName()}[&6{$this->getLeader()->getKills()}]") : TextFormat::colorize("&7{$this->getLeader()->getName()}[&6{$this->getLeader()->getKills()}]")));
        $player->sendMessage(TextFormat::YELLOW . "Co-Leaders: " . implode(", ", $coleaders));
        $player->sendMessage(TextFormat::YELLOW . "Captains: " . implode(", ", $captains));
        $player->sendMessage(TextFormat::YELLOW . "Members: " . implode(", ", $members));
        $player->sendMessage(TextFormat::YELLOW . "Balance: " . TextFormat::BLUE . "$" . round($this->getAmount(self::BALANCE,0)));
        $player->sendMessage(TextFormat::YELLOW . "Deaths until Raidable: " . $this->getDTRColor() . $this->getAmount(self::DTR,0). $this->getDTRSuffix());
        $player->sendMessage(TextFormat::YELLOW . "Points: " . TextFormat::RED . $this->getAmount(self::POINTS,0));
        $player->sendMessage(TextFormat::YELLOW . "KOTH Captures: " . TextFormat::RED . $this->getAmount(self::KOTH_CAPPED, 0));
        if ($this->isRegenerating() || $this->isFreezing()) {
            $player->sendMessage(TextFormat::YELLOW . "Time Until Regen: " . gmdate("i:s", $this->dtrCooldown));
        }
        $player->sendMessage(self::GRAY_LINE);
    }

    public function isRaidable(): bool{
        return ($this->getAmount(self::DTR, 0) <= 0);
    }

    public function getDTRColor(): string{
        $dtrColor = TextFormat::GREEN;
        if ($this->getAmount(self::DTR, 0) / $this->getMaxDTR() <= 0.25) {
            if ($this->isRaidable()) {
                $dtrColor = TextFormat::DARK_RED;
            } else {
                $dtrColor = TextFormat::YELLOW;
            }
        }
        return $dtrColor;
    }

    public function getMaxDTR(): float{
        if ($this->getClaim() instanceof UnraidableClaim){
            return PHP_INT_MAX;
        }elseif(count($this->getMembers()) === 1){
            return 1.1;
        } elseif(count($this->getMembers()) === FactionConfig::getMaxMembers()){
            return FactionConfig::getMaxDTR();
        } else {
            return 50;
        }
    }

    public function getDTRSuffix(): string{
        if ($this->isRegenerating()) {
            if ($this->getOnlineMemberAmount() == 0) {
                return (TextFormat::GRAY . "<");
            } else {
                return (TextFormat::GREEN . "^");
            }
        } else if ($this->isFreezing()) {
            return (TextFormat::RED . "■");
        } else {
            return (TextFormat::GREEN . "<");
        }
    }

    public function save(): void{
        file_put_contents(Loader::getInstance()->getDataFolder() . "factions/" . $this->getSimplyName() . ".yml", $this->getFactionData()->serialize());
    }

    public function log(string $action): void{
        ThreadManager::getInstance()->factionLog($this, $action);
    }

    public function isRegenerating(): bool{
        return $this->regenerating;
    }

    public function isFreezing(): bool{
        return $this->freezing;
    }

    public function setFreezing(int $time): void{
        $this->regenerating = false;
        $this->freezing = true;
        $this->dtrCooldown = $time;
    }

    public function setRegenerating(int $time): void{
        $this->regenerating = true;
        $this->freezing = false;
        $this->dtrCooldown = $time;
    }

    public function unsetCooldown(): void{
        $this->regenerating = false;
        $this->freezing = false;
        $this->dtrCooldown = 0;
    }

    /**
     * @return bool
     */
    public function isNeedsSave(): bool{
        return $this->needsSave;
    }

}