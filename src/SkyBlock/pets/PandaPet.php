<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class PandaPet extends WalkingPet {

    const ENTITY_ID = EntityIds::PANDA;

    public float $height = 1.25;
    public float $width = 1.3;

    public string $name = "Panda";

}