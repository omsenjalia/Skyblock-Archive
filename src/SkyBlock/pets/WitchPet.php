<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class WitchPet extends WalkingPet {

    const ENTITY_ID = EntityIds::WITCH;

    public float $height = 1.95;
    public float $width = 0.6;

    public string $name = "Witch";
}
