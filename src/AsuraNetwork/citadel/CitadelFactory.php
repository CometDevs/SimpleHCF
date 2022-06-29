<?php

namespace AsuraNetwork\citadel;

use AsuraNetwork\Loader;
use AsuraNetwork\session\SessionFactory;
use pocketmine\network\mcpe\protocol\StructureTemplateDataRequestPacket;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

class CitadelFactory{
    use SingletonTrait;

    public static string $PREFIX = TextFormat::DARK_PURPLE . "[Citadel]";

    private array $cappers = [];
    private ?string $lootable = null;

    private array $citadelChests = [];
    private array $citadelLoot = [];
/*
    public function __construct(){
        citadelInfo = new File(HCF.getInstance().getDataFolder(), "citadelInfo.json");

        loadCitadelInfo();
        HCF.getInstance().getServer().getPluginManager().registerEvents(new CitadelListener(), HCF.getInstance());

        (new CitadelSaveTask()).runTaskTimerAsynchronously(HCF.getInstance(), 0L, 20 * 60 * 5);
    }

    public function getActivatedMessage(): string{
        $messages = [TextFormat::GRAY . "███████",
            TextFormat::GRAY . "██" . TextFormat::DARK_PURPLE . "████" . TextFormat::GRAY . "█",
            TextFormat::GRAY . "█" . TextFormat::DARK_PURPLE . "█" . TextFormat::GRAY . "█████ " . TextFormat::DARK_PURPLE . "[Citadel]",
            TextFormat::GRAY . "█" . TextFormat::DARK_PURPLE . "█" . TextFormat::GRAY . "█████ " . TextFormat::DARK_PURPLE . "Activated and",
            TextFormat::GRAY . "█" . TextFormat::DARK_PURPLE . "█" . TextFormat::GRAY . "█████ " . TextFormat::GOLD . "can be contested now.",
            TextFormat::GRAY . "█" . TextFormat::DARK_PURPLE . "█" . TextFormat::GRAY . "█████",
            TextFormat::GRAY . "██" . TextFormat::DARK_PURPLE . "████" . TextFormat::GRAY . "█",
            TextFormat::GRAY . "███████"
        ];
        return implode(TextFormat::EOL, $messages);
    }

    public function getControlMessage(string $teamName, string $playerName): string{
        $messages = [TextFormat::GRAY . "███████",
            TextFormat::GRAY . "██" . TextFormat::DARK_PURPLE . "████" . TextFormat::GRAY . "█",
            TextFormat::GRAY . "█" . TextFormat::DARK_PURPLE . "█" . TextFormat::GRAY . "█████ " . TextFormat::DARK_PURPLE . "[Citadel]",
            TextFormat::GRAY . "█" . TextFormat::DARK_PURPLE . "█" . TextFormat::GRAY . "█████ " . TextFormat::YELLOW . "controlled by",
            TextFormat::GRAY . "█" . TextFormat::DARK_PURPLE . "█" . TextFormat::GRAY . "█████ " . $teamName . TextFormat::WHITE . $playerName,
            TextFormat::GRAY . "█" . TextFormat::DARK_PURPLE . "█" . TextFormat::GRAY . "█████",
            TextFormat::GRAY . "██" . TextFormat::DARK_PURPLE . "████" . TextFormat::GRAY . "█",
            TextFormat::GRAY . "███████"
            ];
    }

    public function init(): void{
        Loader::getInstance()->saveResource("citadel.yml");
        try {
            if (!citadelInfo.exists() && citadelInfo.createNewFile()) {
                BasicDBObject dbo = new BasicDBObject();

                dbo.put("cappers", new HashSet<>());
                dbo.put("lootable", new Date());
                dbo.put("chests", new BasicDBList());
                dbo.put("loot", new BasicDBList());

                FileUtils.write(citadelInfo, Library.GSON.toJson(new JsonParser().parse(dbo.toString())));
            }

            BasicDBObject dbo = (BasicDBObject) JSON.parse(FileUtils.readFileToString(citadelInfo));

            if (dbo != null) {
                this.cappers = new HashSet<>();

                // Conversion
                if (dbo.containsField("capper")) {
                    cappers.add(new ObjectId(dbo.getString("capper")));
                }

                for (String capper : (List<String>) dbo.get("cappers")) {
                    cappers.add(new ObjectId(capper));
                }

                this.lootable = dbo.getDate("lootable");

                BasicDBList chests = (BasicDBList) dbo.get("chests");
                BasicDBList loot = (BasicDBList) dbo.get("loot");

                for (Object chestObj : chests) {
                    BasicDBObject chest = (BasicDBObject) chestObj;
                    citadelChests.add(LocationSerializer.deserialize((BasicDBObject) chest.get("location")));
                }

                for (Object lootObj : loot) {
                    citadelLoot.add(ItemStackSerializer.deserialize((BasicDBObject) lootObj));
                }
            }
        } catch (Exception e) {
    e.printStackTrace();
}
    }

    public function saveCitadelInfo(): void{
        try {
            BasicDBObject dbo = new BasicDBObject();

            dbo.put("cappers", cappers.stream().map(ObjectId::toString).collect(Collectors.toList()));
            dbo.put("lootable", lootable);

            BasicDBList chests = new BasicDBList();
            BasicDBList loot = new BasicDBList();

            for (Location citadelChest : citadelChests) {
                BasicDBObject chest = new BasicDBObject();
                chest.put("location", LocationSerializer.serialize(citadelChest));
                chests.add(chest);
            }

            for (ItemStack lootItem : citadelLoot) {
                loot.add(ItemStackSerializer.serialize(lootItem));
            }

            dbo.put("chests", chests);
            dbo.put("loot", loot);

            citadelInfo.delete();
            FileUtils.write(citadelInfo, Library.GSON.toJson(new JsonParser().parse(dbo.toString())));
        } catch (Exception e) {
    e.printStackTrace();
}
    }

    public function resetCappers(): void{
        $this->cappers = [];
    }

    public function addCapper(Player $capper): void{
        $this->cappers[$capper->getName()] = $capper;
        $this->lootable = $this->generateLootableDate();

        HCF.getInstance().getServer().getPluginManager().callEvent(new CitadelCapturedEvent(capper));
        saveCitadelInfo();
    }

    public function canLootCitadel(Player $player): bool{
    $team = SessionFactory::getInstance()->get($player->getName())?->getFaction();
        return (($team != null && isset($this->cappers[$team->getSimplyName()]) || System.currentTimeMillis() > lootable.getTime());
    }

    // Credit to http://stackoverflow.com/a/3465656 on StackOverflow.
    private function generateLootableDate(): string{
        return date("Y-m-d H:i:s", strtotime("next Wednesday"));
    }

    public function scanLoot() {
citadelChests.clear();

        for (Team team : HCF.getInstance().getTeamHandler().getTeams()) {
            if (team.getOwner() != null) {
                continue;
            }

            if (team.hasDTRBitmask(DTRBitmask.CITADEL)) {
                for (Claim claim : team.getClaims()) {
                    for (Location location : new CuboidRegion("Citadel", claim.getMinimumPoint(), claim.getMaximumPoint())) {
                        if (location.getBlock().getType() == Material.CHEST) {
                            citadelChests.add(location);
                        }
                    }
                }
            }
        }
    }

    public int respawnCitadelChests() {
            int respawned = 0;

        for (Location chest : citadelChests) {
            if (respawnCitadelChest(chest)) {
                respawned..;
            }
        }

        return (respawned);
    }

    public boolean respawnCitadelChest(Location location) {
                BlockState blockState = location.getBlock().getState();

        if (blockState instanceof Chest) {
            Chest chest = (Chest) blockState;

            chest.getBlockInventory().clear();
            chest.getBlockInventory().addItem(citadelLoot.get(Library.RANDOM.nextInt(citadelLoot.size())));
            return (true);
        } else {
            HCF.getInstance().getLogger().warning("Citadel chest defined at [" . location.getBlockX() . ", " . location.getBlockY() . ", " . location.getBlockZ() . "] isn't a chest!");
            return (false);
        }
    }
*/
}