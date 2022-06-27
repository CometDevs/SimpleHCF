<?php
namespace AsuraNetwork\koth;

use AsuraNetwork\Loader;
use pocketmine\player\Player;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
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
      $position = new Vector3($coordinates[0], $coordinates[1], $coordinates[2]);
      $this->add(new Koth($kothData["name"], $world, $position, $kothData["rewards"]));
    }
    Loader::getInstance()->getLogger()->info("");
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
  * @return void
  */
  public function createKOTH(): void {}

}
