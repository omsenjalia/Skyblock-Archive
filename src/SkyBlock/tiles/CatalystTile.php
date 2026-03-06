<?php

namespace SkyBlock\tiles;

use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\NameableTrait;
use pocketmine\block\tile\Spawnable;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Facing;
use pocketmine\nbt\tag\CompoundTag;
use SkyBlock\block\Catalyst;
use SkyBlock\Main;

class CatalystTile extends Spawnable implements Nameable {
    private int $delay = 200;

    use NameableTrait;

    public function onUpdate() : bool {
        if ($this->closed) return false;
        $block = $this->getBlock();
        if (!$block instanceof Catalyst) return true;
        if ($this->delay <= 0) {
            $this->delay = 200;
            if (($island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($this->getPosition()->getWorld()->getDisplayName())) === null) return false;

            $up = $this->getPosition()->getWorld()->getBlock($block->getSide(Facing::UP)->getPosition());
            if ($up->getTypeId() === BlockTypeIds::AIR) {
                $this->getPosition()->getWorld()->setBlock($up->getPosition()->asVector3(), $this->getGenBlock(), false);
            }
        } else {
            $this->delay--;
        }
        return true;
    }

    private function getGenBlock() : Block {
        $island = Main::getInstance()->getIslandManager()->getOnlineIslandByWorld($this->getPosition()->getWorld()->getDisplayName());
        $oredata = $island->getOreDataPrefArray();

        $newBlock = $this->randomizeWithPercentage($oredata);
        $newBlock = match ($newBlock) {
            "coal" => VanillaBlocks::COAL_ORE(),
            "copper" => VanillaBlocks::COPPER_ORE(),
            "iron" => VanillaBlocks::IRON_ORE(),
            "lapis" => VanillaBlocks::LAPIS_LAZULI_ORE(),
            "gold" => VanillaBlocks::GOLD_ORE(),
            "diamond" => VanillaBlocks::DIAMOND_ORE(),
            "emerald" => VanillaBlocks::EMERALD_ORE(),
            "quartz" => VanillaBlocks::NETHER_QUARTZ_ORE(),
            "netherite" => VanillaBlocks::ANCIENT_DEBRIS(),
            "deep_coal" => VanillaBlocks::DEEPSLATE_COAL_ORE(),
            "deep_copper" => VanillaBlocks::DEEPSLATE_COPPER_ORE(),
            "deep_iron" => VanillaBlocks::DEEPSLATE_IRON_ORE(),
            "deep_lapis" => VanillaBlocks::DEEPSLATE_LAPIS_LAZULI_ORE(),
            "deep_gold" => VanillaBlocks::DEEPSLATE_GOLD_ORE(),
            "deep_diamond" => VanillaBlocks::DEEPSLATE_DIAMOND_ORE(),
            "deep_emerald" => VanillaBlocks::DEEPSLATE_EMERALD_ORE(),
            "deep_quartz" => VanillaBlocks::QUARTZ(),
            "deep_netherite" => VanillaBlocks::NETHERITE(),
            default => VanillaBlocks::COBBLESTONE(),
        };
        return $newBlock;
    }

    private function randomizeWithPercentage($array) : int|string {
        $totalWeight = array_sum($array);
        $rand = rand(1, $totalWeight);

        $currentWeight = 0;
        foreach ($array as $key => $weight) {
            $currentWeight += intval($weight);
            if ($rand <= $currentWeight) {
                return $key;
            }
        }
        return "default";
    }

    public function getName() : string {
        return "Catalyst";
    }

    public function getDefaultName() : string {
        return "Catalyst";
    }

    public function readSaveData(CompoundTag $nbt) : void {
    }

    protected function writeSaveData(CompoundTag $nbt) : void {
    }
}