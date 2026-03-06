<?php

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class BlazePet extends WalkingPet {

    const ENTITY_ID = EntityIds::BLAZE;
    public string $name = "Blaze";
    public float $height = 1.8;
    public float $width = 0.5;
}