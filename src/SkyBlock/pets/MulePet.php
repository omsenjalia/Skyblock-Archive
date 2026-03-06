<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class MulePet extends WalkingPet {

    const ENTITY_ID = EntityIds::MULE;

    public string $name = "Mule";

    public float $width = 1.3965;
    public float $height = 1.6;
}
