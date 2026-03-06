<?php

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class CowPet extends WalkingPet {

    const ENTITY_ID = EntityIds::COW;
    public string $name = "Cow";
    public float $height = 1.3;
    public float $width = 0.9;
}