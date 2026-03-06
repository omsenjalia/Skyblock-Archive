<?php

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class PigPet extends WalkingPet {

    const ENTITY_ID = EntityIds::PIG;
    public string $name = "Pig";
    public float $height = 0.9;
    public float $width = 0.9;
}