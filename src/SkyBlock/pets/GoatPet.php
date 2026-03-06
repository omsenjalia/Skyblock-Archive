<?php

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class GoatPet extends WalkingPet {

    const ENTITY_ID = EntityIds::GOAT;
    public string $name = "Goat";
    public float $height = 1.3;
    public float $width = 0.9;
}