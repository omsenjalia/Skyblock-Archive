<?php

declare(strict_types=1);

namespace SkyBlock\pets;

use Exception;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;

class EvokerPet extends WalkingPet {

    const ENTITY_ID = EntityIds::EVOCATION_ILLAGER;

    public string $name = "Evoker";

    public float $width = 0.6;
    public float $height = 1.95;

    public function generateCustomPetData() : void {
        try {
            $isCasting = mt_rand(0, 1);
            $this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::EVOKER_SPELL, (bool) $isCasting);
        } catch (Exception) {
        }
    }

}