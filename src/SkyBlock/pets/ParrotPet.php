<?php

namespace SkyBlock\pets;

use Exception;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class ParrotPet extends HoveringPet implements SmallCreature {
    const ENTITY_ID = EntityIds::PARROT;
    public string $name = "Parrot";
    public float $width = 1.0;
    public float $height = 0.5;

    public function generateCustomPetData() : void {
        try {
            $randomVariant = mt_rand(0, 3);
            $this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, $randomVariant);
        } catch (Exception) {
        }
    }
}