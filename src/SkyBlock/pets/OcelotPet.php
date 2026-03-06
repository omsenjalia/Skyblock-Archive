<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use Exception;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class OcelotPet extends WalkingPet implements SmallCreature {

    const ENTITY_ID = EntityIds::OCELOT;

    public string $name = "Ocelot";

    public float $width = 0.6;
    public float $height = 0.7;

    public function generateCustomPetData() : void {
        try {
            $randomVariant = mt_rand(0, 3);
            $this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, $randomVariant);
        } catch (Exception) {
        }

    }

}
