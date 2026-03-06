<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class SlimePet extends BouncingPet implements SmallCreature {

    const ENTITY_ID = EntityIds::SLIME;

    public float $height = 0.51;
    public float $width = 0.51;

    public string $name = "Slime";
}