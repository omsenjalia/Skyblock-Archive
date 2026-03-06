<?php

namespace SkyBlock\spawner;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;

abstract class Animal extends Creature implements Ageable {

    protected bool $baby = false;

    protected function initEntity(CompoundTag $nbt) : void {
        parent::initEntity($nbt);
        $this->baby = (bool) $nbt->getByte("isBaby", 0);
    }

    protected function syncNetworkData(EntityMetadataCollection $properties) : void {
        parent::syncNetworkData($properties);
        $properties->setGenericFlag(EntityMetadataFlags::BABY, $this->isBaby());
    }

    public function isBaby() : bool {
        return $this->baby;
    }

    public function setBaby(bool $baby) : void {
        $this->baby = $baby;
        $this->networkPropertiesDirty = true;
    }

}
