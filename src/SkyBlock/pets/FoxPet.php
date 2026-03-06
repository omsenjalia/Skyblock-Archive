<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class FoxPet extends WalkingPet implements SmallCreature {

    const ENTITY_ID = EntityIds::FOX;

    public string $name = "Fox";

    public float $width = 0.7;
    public float $height = 0.6;

}
