<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class RabbitPet extends BouncingPet implements SmallCreature {

    const ENTITY_ID = EntityIds::RABBIT;

    public float $height = 0.5;
    public float $width = 0.4;

    public string $name = "Rabbit";

    public function generateCustomPetData() : void {
        $variants = [
            0, 1, 2, 3, 4, 5, 99
        ];
        $randomVariant = $variants[array_rand($variants)];
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, $randomVariant);
    }
}