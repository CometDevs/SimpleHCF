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
  
  /** @var Vector3|null $pos1 */
  private ?Vector3 $pos1;
  
  /** @var Vector3|null $pos2 */
  private ?Vector3 $pos2;
  
  /** @var string[] $rewards */
  private array $rewards = [];
  
  /** @var bool $enabled */
  private bool $enabled = false;
  
  /**
   * Koth constructor.
   * @param string $name
   * @param World $world
   * @param Vector3 $position
   * @param array $rewards
   */
  public function __construct(string $name, World $world, Vector3 $pos1, Vector3 $pos2, array $rewards) {
    $this->name = $name;
    $this->world = $world;
    $this->pos1 = $pos1;
    $this->pos1 = $pos2;
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
  public function getPos1(): ?Vector3 {
    return $this->pos1;
  }
  
  /**
   * @return Vector3|null
   */
  public function getPos2(): ?Vector3 {
    return $this->pos2;
  }
  
  /**
   * @return array
   */
  public function getRewards(): array {
    return $this->rewards;
  }
  
  /**
   * @return bool
   */
  public function isEnabled(): bool {
    return $this->enabled;
  }
  
  /**
   * @param bool $value
   * @return void
   */
  public function setEnabled(bool $value): void {
    $this->enabled = $value;
  }
}
