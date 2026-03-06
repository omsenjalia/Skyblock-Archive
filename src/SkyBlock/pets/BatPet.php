<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class BatPet extends HoveringPet implements SmallCreature {
    const ENTITY_ID = EntityIds::BAT;
    public string $name = "Bat";
    public float $width = 0.5;
    public float $height = 0.9;
}