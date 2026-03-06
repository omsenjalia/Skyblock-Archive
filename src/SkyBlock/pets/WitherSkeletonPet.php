<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class WitherSkeletonPet extends WalkingPet {

    const ENTITY_ID = EntityIds::WITHER_SKELETON;

    public float $height = 2.4;
    public float $width = 0.7;

    public string $name = "Wither Skeleton";
}
