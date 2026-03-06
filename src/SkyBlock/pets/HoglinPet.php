<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class HoglinPet extends WalkingPet {

    const ENTITY_ID = EntityIds::HOGLIN;

    public float $height = 0.9;
    public float $width = 0.9;

    public string $name = "Hoglin";
}