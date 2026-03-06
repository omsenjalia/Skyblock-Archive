<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class MagmaCubePet extends BouncingPet implements SmallCreature {

    const ENTITY_ID = EntityIds::MAGMA_CUBE;

    public float $height = 0.51;
    public float $width = 0.51;

    public string $name = "Magma Cube";

}