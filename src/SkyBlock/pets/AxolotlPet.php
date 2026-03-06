<?php

namespace SkyBlock\pets;

use Exception;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class AxolotlPet extends HoveringPet implements SmallCreature {
    const ENTITY_ID = EntityIds::AXOLOTL;
    public string $name = "Axolotl";
    public float $width = 0.42;
    public float $height = 0.75;

    public function generateCustomPetData() : void {
        try {
            $randomVariant = mt_rand(0, 4);
            $this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, $randomVariant);
        } catch (Exception) {
        }
    }
}