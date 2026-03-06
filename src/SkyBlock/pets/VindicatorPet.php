<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class VindicatorPet extends WalkingPet {

    const ENTITY_ID = EntityIds::VINDICATOR;

    public string $name = "Vindicator";

    public float $width = 0.6;
    public float $height = 1.95;
}