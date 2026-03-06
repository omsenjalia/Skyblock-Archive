<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class WitherPet extends HoveringPet {

    const ENTITY_ID = EntityIds::WITHER;

    public float $height = 3.5;
    public float $width = 0.9;

    public string $name = "Wither";
}
