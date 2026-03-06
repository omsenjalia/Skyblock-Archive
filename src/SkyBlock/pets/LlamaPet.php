<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use Exception;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class LlamaPet extends WalkingPet {

    const ENTITY_ID = EntityIds::LLAMA;

    public float $height = 0.935;
    public float $width = 0.45;

    public string $name = "Llama";

    public function generateCustomPetData() : void {
        try {
            $randomVariant = mt_rand(0, 3);
            $this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, $randomVariant);
        } catch (Exception) {
        }
    }

}
