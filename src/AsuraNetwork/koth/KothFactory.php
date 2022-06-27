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
        $this->registerKothData($data["name"], $data["world"], $data["position"], $data["rewards"]);
      }
    }
    foreach ($this->getKothsData() as $kothData) {
      $world = Server::getInstance()->getWorldManager()->getWorldByName($kothData["world"]);
      $coordinates = explode(":", $kothData["position"]);
      $position = new Vector3((int)$coordinates[0], (int)$coordinates[1], (int)$coordinates[2]);
      $this->add(new Koth($kothData["name"], $world, $position, $kothData["rewards"]));
    }
    Loader::getInstance()->getLogger()->info(TextFormat::YELLOW . "All koths have been loaded, number of koths loaded: " . count($this->getKoths()));
  }

  /**
  * @param string $name
  * @param string $worldName
  * @param string $position
  * @param array $rewards
  * @return void
  */
  public function registerKothData(string $name, string $worldName, string $position, array $rewards): void {
    $data = [
      "name" => $name,
      "world" => $worldName,
      "position" => $position,
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
  * @return void
  */
  public function createKOTH(string $name, World $world, Vector3 $position, array $rewards): void {
    $config = new Config(Loader::getInstance()->getDataFolder() . $name . ".yml", Config::YAML);
    $savedPos = $position->getX() . ":" . $position->getY() . ":" . $position->getZ();
    $data = [
      "name" => $name,
      "world" => $world->getName(),
      "position" => $savedPos,
      "rewards" => $rewards
      ];
    $config->setAll($data);
    $config->save();
    $koth = new Koth($name, $world, $position, $rewards);
    $this->add($koth);
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