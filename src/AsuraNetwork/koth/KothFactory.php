<?php
namespace AsuraNetwork\koth;

use AsuraNetwork\Loader;
use pocketmine\player\Player;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\Server;

/**
* Class KothFactory
* @package AsuraNetwork\koth
*/
class KothFactory {

  /** @var Koth[] $koths */
  private array $koths = [];

  /** @var array $data */
  private array $data = [];
  
  /**
   * @return void
   */
  public function init(): void {
    if (!is_dir(Loader::getInstance()->getDataFolder() . "koths")) @mkdir(Loader::getInstance()->getDataFolder() . "koths");
    foreach (glob(Loader::getInstance()->getDataFolder() . "koths/" . "*.yml") as $koths) {
      $config = new Config(Loader::getInstance()->getDataFolder() . $koths, Config::YAML);
      foreach ($config->getAll() as $data) {
        $this->registerKothData($data["name"], $data["world"], $data["positions"]["pos1"], $data["positions"]["pos2"], $data["rewards"]);
      }
    }
    foreach ($this->getKothsData() as $kothData) {
      $world = Server::getInstance()->getWorldManager()->getWorldByName($kothData["world"]);
      $this->add(new Koth($kothData["name"], $world, $kothData["rewards"]));
      if ($kothData["positions"]["pos1"] != null and $kothData["positions"]["pos2"] != null) {
        $posToString1 = explode(":", $kothData["positions"]["pos1"]);
        $pos1 = new Vector3((int)$posToString1[0], (int)$posToString1[1], (int)$posToString1[2]);
        $this->get($kothData["name"])->setPos1($pos1);
        $posToString2 = explode(":", $kothData["positions"]["pos2"]);
        $pos2 = new Vector3((int)$posToString2[0], (int)$posToString2[1], (int)$posToString2[2]);
        $this->get($kothData["name"])->setPos2($pos2);
      }
    }
    Loader::getInstance()->getLogger()->info(TextFormat::YELLOW . "All koths have been loaded, number of koths loaded: " . count($this->getKoths()));
  }

  /**
  * @param string $name
  * @param string $worldName
  * @param string $pos1
  * @param string $pos2
  * @param array $rewards
  * @return void
  */
  public function registerKothData(string $name, string $worldName, string $pos1 = null, string $pos2 = null, array $rewards): void {
    $data = [
      "name" => $name,
      "world" => $worldName,
      "positions" => [
        "pos1" => $pos1,
        "pos2" => $pos2
        ],
      "rewards" => $rewards
    ];
    $this->data[$name] = $data;
  }

  /**
  * @return array
  */
  public function getKothsData(): array {
    return $this->data;
  }

  /**
  * @param Koth $koth
  * @return void
  */
  public function add(Koth $koth): void {
    $this->koths[$koth->getName()] = $koth;
  }
  
  /**
   * @param string $kothName
   * @return Koth|null
   */
  public function get(string $kothName): ?Koth {
    return $this->koths[$kothName] ?? null;
  }
  
  /**
   * @param string $kothName
   * @return bool
   */
  public function exist(string $kothName): bool {
    return isset($this->koths[$kothName]);
  }

  /**
   * @param string $name
   * @param World $world
   * @param array $rewards
  * @return void
  */
  public function createKOTH(string $name, World $world, array $rewards): void {
    $config = new Config(Loader::getInstance()->getDataFolder() . $name . ".yml", Config::YAML);
    $data = [
      "name" => $name,
      "world" => $world->getName(),
      "positions" => [
        "pos1" => null,
        "pos2" => null
        ],
      "rewards" => $rewards
      ];
    $config->setAll($data);
    $config->save();
    $koth = new Koth($name, $world, $rewards);
    $this->add($koth);
  }
  
  /**
   * @param string $kothName
   * @param Vector3 $position
   * @return void
   */
  public function setKothPos1(string $kothName, Vector3 $position): void {
    $this->get($kothName)->setPos1($position);
    $config = new Config(Loader::getInstance()->getDataFolder() . $kothName . ".yml", Config::YAML);
    $config->setNested("positions", [
      "pos1" => $position->getX() . ":" . $position->getY() . ":" . $position->getZ()
      ]);
    $config->save();
  }
  
  /**
   * @param string $kothName
   * @param Vector3 $position
   * @return void
   */
  public function setKothPos2(string $kothName, Vector3 $position): void {
    $this->get($kothName)->setPos2($position);
    $config = new Config(Loader::getInstance()->getDataFolder() . $kothName . ".yml", Config::YAML);
    $config->setNested("positions", [
      "pos2" => $position->getX() . ":" . $position->getY() . ":" . $position->getZ()
      ]);
    $config->save();
  }
  
  /**
   * @param string $kothName
   * @return void
   */
  public function deleteKOTH(string $kothName): void {
    if (!$this->exist($kothName)) return;
    if ($this->get($kothName)->isEnabled()) $this->get($kothName)->setEnabled(false);
    unset($this->koths[$kothName]);
    if (file_exists(Loader::getInstance()->getDataFolder() . $kothName . ".yml")) {
    unlink(Loader::getInstance()->getDataFolder() . $kothName . ".yml");
    }
    Loader::getInstance()->getLogger()->info(TextFormat::GREEN . "Koth " . $kothName . " was removed successfully.");
  }
  
  /**
   * @return array
   */
  public function getKoths(): array {
    return $this->koths;
  }
}