<?php

namespace SkyBlock\pets;

use Exception;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class SnowFoxPet extends WalkingPet implements SmallCreature {

    const ENTITY_ID = EntityIds::FOX;
    public string $name = "SnowFox";
    public float $width = 0.7;
    public float $height = 0.6;

    public function generateCustomPetData() : void {
        try {
            $this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, 1);
        } catch (Exception) {
        }
    }

}