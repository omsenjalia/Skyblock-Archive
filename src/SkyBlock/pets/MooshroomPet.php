<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class MooshroomPet extends WalkingPet {

    const ENTITY_ID = EntityIds::MOOSHROOM;

    public float $height = 1.4;
    public float $width = 0.9;

    public string $name = "Mooshroom";

}
