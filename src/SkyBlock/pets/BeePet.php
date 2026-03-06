<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class BeePet extends HoveringPet implements SmallCreature {

    const ENTITY_ID = EntityIds::BEE;

    public string $name = "Bee";

    public float $width = 0.6;
    public float $height = 0.6;

}
