<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;

class SnowGolemPet extends WalkingPet {

    const ENTITY_ID = EntityIds::SNOW_GOLEM;

    public float $height = 1.9;
    public float $width = 0.7;

    public string $name = "Snow Golem";

    public function generateCustomPetData() : void {
        $this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::SHEARED, true);
    }

}
