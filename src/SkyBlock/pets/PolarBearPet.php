<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class PolarBearPet extends WalkingPet {

    const ENTITY_ID = EntityIds::POLAR_BEAR;

    public float $height = 1.4;
    public float $width = 1.3;

    public string $name = "Polar Bear";
}