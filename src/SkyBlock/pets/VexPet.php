<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class VexPet extends HoveringPet implements SmallCreature {

    const ENTITY_ID = EntityIds::VEX;

    public float $height = 0.8;
    public float $width = 0.4;

    public string $name = "Vex";

    public function generateCustomPetData() : void {
        //        $this->canCollide = false;
    }
}