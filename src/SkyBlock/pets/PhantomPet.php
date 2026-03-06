<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class PhantomPet extends HoveringPet implements SmallCreature {
    const ENTITY_ID = EntityIds::PHANTOM;
    public string $name = "Phantom";
    public float $width = 0.5;
    public float $height = 0.9;
}