<?php

namespace SkyBlock\enchants\block;

use pocketmine\block\Crops;
use pocketmine\block\Farmland;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\Wheat;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\Hoe;
use pocketmine\item\VanillaItems;
use pocketmine\math\Facing;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use SkyBlock\EvFunctions;
use SkyBlock\item\Axe;
use SkyBlock\Main;

class Replanter extends BaseBlockBreakEnchant {

    public function isApplicableTo(Player $holder, int $level = 0) : bool {
        return $holder->getInventory()->getItemInHand() instanceof Hoe || $holder->getInventory()->getItemInHand() instanceof Axe || $holder->getInventory()->getItemInHand() instanceof \pocketmine\item\Axe;
    }

    public function onActivation(Player $player, BlockBreakEvent $ev, int $enchantmentlevel) : void {
        $block = $ev->getBlock();
        if (!$block instanceof Crops || !$block->getSide(Facing::DOWN) instanceof Farmland) {
            return;
        }
        if (!EvFunctions::isFarmRipe($block)) {
            return;
        }
        $seed = $block === VanillaBlocks::WHEAT() ? VanillaItems::WHEAT_SEEDS() : $block->getPickedItem();
        if (!$player->getInventory()->contains($seed)) {
            return;
        }
        Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(fn() => $block->getPosition()->getWorld()->setBlock($block->getPosition(), $seed->getBlock())), 1);
        $player->getInventory()->removeItem($seed);
    }

}