<?php

namespace SkyBlock\spawner;

use pocketmine\nbt\tag\CompoundTag;
use SkyBlock\StackFactory;

trait StackableTrait {

    public int $stackAmount = 1;

    public function getStackAmount() : int {
        return $this->stackAmount;
    }

    public function saveNBT() : CompoundTag {
        $nbt = parent::saveNBT();
        $nbt->setInt(StackFactory::TAG_STACK_AMOUNT, $this->stackAmount);
        return $nbt;
    }

    public function setStack(int $amount = 1) : void {
        $this->stackAmount = $amount;
    }

    protected function initEntity(CompoundTag $nbt) : void {
        $this->stackAmount = $nbt->getInt(StackFactory::TAG_STACK_AMOUNT, 1);
        parent::initEntity($nbt);
    }

}