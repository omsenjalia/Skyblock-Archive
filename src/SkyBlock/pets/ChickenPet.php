<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ChickenPet extends WalkingPet implements SmallCreature {

    public const ENTITY_ID = EntityIds::CHICKEN;

    public string $name = "Chicken";

    public float $width = 0.4;
    public float $height = 0.7;

}
