<?php

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class AllayPet extends HoveringPet implements SmallCreature {
    const ENTITY_ID = EntityIds::ALLAY;
    public string $name = "Allay";
    public float $width = 0.6;
    public float $height = 0.6;
}