<?php


namespace SkyBlock\item;


use pocketmine\block\Block;
use pocketmine\block\BlockToolType;
use pocketmine\entity\Entity;

class Pickaxe extends TieredTool {
    public function getBlockToolType() : int {
        return BlockToolType::PICKAXE;
    }

    public function getBlockToolHarvestLevel() : int {
        return $this->tier->getHarvestLevel();
    }

    public function getAttackPoints() : int {
        return $this->tier->getBaseAttackPoints() - 2;
    }

    public function onDestroyBlock(Block $block, array &$returnedItems) : bool {
        if (!$block->getBreakInfo()->breaksInstantly()) {
            return $this->applyDamage(1);
        }
        return false;
    }

    public function onAttackEntity(Entity $victim, array &$returnedItems) : bool {
        return $this->applyDamage(2);
    }
}
