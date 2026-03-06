<?php

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ZombiePet extends WalkingPet {

    const ENTITY_ID = EntityIds::ZOMBIE;
    public string $name = "Zombie";
    public float $height = 1.9;
    public float $width = 0.6;
}