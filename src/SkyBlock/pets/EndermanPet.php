<?php

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class EndermanPet extends WalkingPet {

    const ENTITY_ID = EntityIds::ENDERMAN;
    public string $name = "Enderman";
    public float $width = 2.9;
    public float $height = 0.6;

}
