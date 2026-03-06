<?php

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class GlowSquidPet extends HoveringPet {
    const ENTITY_ID = EntityIds::GLOW_SQUID;
    public string $name = "GlowSquid";
    public float $width = 0.95;
    public float $height = 0.95;
}