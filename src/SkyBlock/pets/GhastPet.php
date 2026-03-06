<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class GhastPet extends HoveringPet {

    const ENTITY_ID = EntityIds::GHAST;

    public float $width = 4.0;
    public float $height = 4.0;

    public string $name = "Ghast";
}
