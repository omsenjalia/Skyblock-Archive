<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class DonkeyPet extends WalkingPet {

    const ENTITY_ID = EntityIds::DONKEY;
    public string $name = "Donkey";
    public float $width = 1.3965;
    public float $height = 1.6;

}
