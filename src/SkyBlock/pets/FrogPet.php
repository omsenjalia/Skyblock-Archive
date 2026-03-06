<?php

namespace SkyBlock\pets;

use Exception;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class FrogPet extends WalkingPet implements SmallCreature {
    const ENTITY_ID = EntityIds::FROG;
    public string $name = "Frog";
    public float $height = 0.5;
    public float $width = 0.5;

    public function generateCustomPetData() : void {
        try {
            $randomVariant = mt_rand(0, 2);
            $this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, $randomVariant);
        } catch (Exception) {
        }
    }

}
