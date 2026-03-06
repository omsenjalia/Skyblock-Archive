<?php

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ElderGuardianPet extends HoveringPet {

    const ENTITY_ID = EntityIds::ELDER_GUARDIAN;
    public string $name = "ElderGuardian";
    public float $width = 2;
    public float $height = 2;
}