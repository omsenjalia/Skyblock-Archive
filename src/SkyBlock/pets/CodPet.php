<?php

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class CodPet extends HoveringPet implements SmallCreature {
    const ENTITY_ID = EntityIds::COD;
    public string $name = "Cod";
    public float $height = 0.3;
    public float $width = 0.6;
}