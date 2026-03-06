<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use Exception;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class WolfPet extends WalkingPet implements SmallCreature {

    const ENTITY_ID = EntityIds::WOLF;

    public string $name = "Wolf";

    public float $width = 0.6;
    public float $height = 0.85;

    public function generateCustomPetData() : void {
        try {
            $randomColour = mt_rand(0, 15);
            $eid = 123456789123456789;
            $this->getNetworkProperties()->setLong(EntityMetadataProperties::OWNER_EID, $eid);
            $this->getNetworkProperties()->setByte(EntityMetadataProperties::COLOR, $randomColour);
        } catch (Exception) {
        }

    }

}
