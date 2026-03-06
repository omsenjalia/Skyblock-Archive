<?php

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class IronGolemPet extends WalkingPet {

    const ENTITY_ID = EntityIds::IRON_GOLEM;
    public string $name = "IronGolem";
    public float $height = 2.9;
    public float $width = 1.4;
}
