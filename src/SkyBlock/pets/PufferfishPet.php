<?php

namespace SkyBlock\pets;

use Exception;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class PufferfishPet extends HoveringPet implements SmallCreature {
    const ENTITY_ID = EntityIds::PUFFERFISH;
    public string $name = "Pufferfish";
    public float $width = 0.96;
    public float $height = 0.96;

    public function generateCustomPetData() : void {
        try {
            $randomVariant = mt_rand(0, 2);
            $this->getNetworkProperties()->setInt(EntityMetadataProperties::PUFFERFISH_SIZE, $randomVariant);
        } catch (Exception) {
        }
    }
}