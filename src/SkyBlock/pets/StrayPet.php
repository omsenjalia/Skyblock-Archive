<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class StrayPet extends WalkingPet {

    const ENTITY_ID = EntityIds::STRAY;

    public float $height = 1.99;
    public float $width = 0.6;

    public string $name = "Stray";
}
