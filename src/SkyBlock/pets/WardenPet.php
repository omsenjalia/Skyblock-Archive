<?php

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class WardenPet extends WalkingPet {

    const ENTITY_ID = EntityIds::WARDEN;

    public string $name = "Warden";

    public float $width = 0.9;
    public float $height = 2.9;
}