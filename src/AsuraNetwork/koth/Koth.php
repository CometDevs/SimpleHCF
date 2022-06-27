<?php
namespace AsuraNetwork\koth;

use pocketmine\world\World;
use pocketmine\math\Vector3;

/**
 * Class Koth
 * @package AsuraNetwork\koth
 */
class Koth {
  
  /** @var string $name */
  private string $name;
  
  /** @var World|null $world */
  private ?World $world;
  
  /** @var Vector3|null $position */
  private ?Vector3 $position;
  
  /** @var string[] $rewards */
  private array $rewards = [];
  
  /**
   * Koth constructor.
   * @param string $name
   * @param World $world
   * @param Vector3 $position
   * @param array $rewards
   */
  public function __construct(string $name, World $world, Vector3 $position, array $rewards) {
    $this->name = $name;
    $this->world = $world;
    $this->position = $position;
    $this->rewards = $rewards;
  }
  
  /**
   * @return string
   */
  public function getName(): string {
    return $this->name;
  }
  
  /**
   * @return World|null
   */
  public function getWorld(): ?World {
    return $this->world;
  }
  
  /**
   * @return Vector3|null
   */
  public function getPosition(): ?Vector3 {
    return $this->position;
  }
  
  /**
   * @return array
   */
  public function getRewards(): array {
    return $this->rewards;
  }
}
