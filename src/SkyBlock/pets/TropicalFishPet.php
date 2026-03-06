<?php

namespace SkyBlock\pets;

use Exception;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class TropicalFishPet extends HoveringPet implements SmallCreature {
    const ENTITY_ID = EntityIds::TROPICALFISH;
    public string $name = "TropicalFish";
    public float $height = 0.52;
    public float $width = 0.52;

    public function generateCustomPetData() : void {
        try {
            $randomVariant = mt_rand(0, 2700);
            $this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, $randomVariant);
        } catch (Exception) {
        }
    }
}