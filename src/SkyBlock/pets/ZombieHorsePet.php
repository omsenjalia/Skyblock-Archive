<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ZombieHorsePet extends WalkingPet {

    const ENTITY_ID = EntityIds::ZOMBIE_HORSE;

    public string $name = "Zombie Horse";

    public float $width = 1.3965;
    public float $height = 1.6;
}
