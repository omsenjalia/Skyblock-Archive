<?php

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ZoglinPet extends WalkingPet {

    const ENTITY_ID = EntityIds::ZOGLIN;
    public string $name = "Zoglin";
    public float $height = 1.4;
    public float $width = 1.3965;
}