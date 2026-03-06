<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class EnderDragonPet extends HoveringPet {

    public const ENTITY_ID = EntityIds::ENDER_DRAGON;
    public string $name = "EnderDragon";
    public float $width = 2.5;
    public float $height = 3;
}
