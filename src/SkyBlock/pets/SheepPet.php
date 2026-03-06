<?php

namespace SkyBlock\pets;

use Exception;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class SheepPet extends WalkingPet {

    const ENTITY_ID = EntityIds::SHEEP;
    public string $name = "Sheep";
    public float $height = 1.3;
    public float $width = 0.9;

    public function generateCustomPetData() : void {
        try {
            $randomVariant = mt_rand(0, 15);
            $this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, $randomVariant);
        } catch (Exception) {
        }
    }

}