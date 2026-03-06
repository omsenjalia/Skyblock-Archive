<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ZombieVillagerPet extends WalkingPet {

    const ENTITY_ID = EntityIds::ZOMBIE_VILLAGER;

    public float $height = 1.95;
    public float $width = 0.6;

    public string $name = "Zombie Villager";
}
