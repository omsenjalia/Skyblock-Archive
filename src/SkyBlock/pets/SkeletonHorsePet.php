<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class SkeletonHorsePet extends WalkingPet {

    const ENTITY_ID = EntityIds::SKELETON_HORSE;

    public string $name = "Skeleton Horse";

    public float $width = 1.3965;
    public float $height = 1.6;
}
