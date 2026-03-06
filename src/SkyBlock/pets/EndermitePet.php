<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class EndermitePet extends WalkingPet implements SmallCreature {
    const ENTITY_ID = EntityIds::ENDERMITE;
    public float $height = 0.3;
    public float $width = 0.4;
    public string $name = "Endermite";

}
