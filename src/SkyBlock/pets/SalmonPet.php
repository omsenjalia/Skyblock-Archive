<?php

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class SalmonPet extends HoveringPet implements SmallCreature {
    const ENTITY_ID = EntityIds::SALMON;
    public string $name = "Salmon";
    public float $width = 0.5;
    public float $height = 0.5;
}