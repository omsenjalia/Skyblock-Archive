<?php

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class TurtlePet extends WalkingPet {

    const ENTITY_ID = EntityIds::TURTLE;
    public string $name = "Turtle";
    public float $height = 0.4;
    public float $width = 1.2;
}