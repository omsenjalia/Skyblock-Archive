<?php

namespace SkyBlock\enchants\block;

use pocketmine\block\BlockTypeIds;
use pocketmine\block\Sugarcane;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Hoe;
use pocketmine\item\VanillaItems;
use pocketmine\math\Facing;
use pocketmine\player\Player;
use SkyBlock\item\Axe;
use function Symfony\Component\String\b;

class Blessing extends BaseBlockBreakEnchant {

    public function isApplicableTo(Player $holder, int $level = 0) : bool {
        return $holder->getInventory()->getItemInHand() instanceof Hoe || $holder->getInventory()->getItemInHand() instanceof Axe || $holder->getInventory()->getItemInHand() instanceof \pocketmine\item\Axe;
    }

    public function onActivation(Player $player, BlockBreakEvent $ev, int $enchantmentlevel) : void {
        $block = $ev->getBlock();
        switch ($block->getTypeId()) {
            case BlockTypeIds::SUGARCANE:
                $item = VanillaBlocks::SUGARCANE()->asItem()->setCount(ceil(3 + 0.5 * $enchantmentlevel) * (2 ? $block->getSide(Facing::UP) === VanillaBlocks::SUGARCANE() : 1));
                if ($player->getInventory()->canAddItem($item)) {
                    $player->getInventory()->addItem($item);
                }
                break;
            case BlockTypeIds::BEETROOTS:
                $item = VanillaItems::BEETROOT()->setCount(ceil(3 + 0.5 * $enchantmentlevel));
                if ($player->getInventory()->canAddItem($item)) {
                    $player->getInventory()->addItem($item);
                }
                break;
            case BlockTypeIds::WHEAT:
                $item = VanillaItems::WHEAT()->setCount(mt_rand(1, ceil(3 + 0.5 * $enchantmentlevel)));
                if ($player->getInventory()->canAddItem($item)) {
                    $player->getInventory()->addItem($item);
                }
                $item = VanillaItems::WHEAT_SEEDS()->setCount(mt_rand(1, ceil(3 + 0.5 * $enchantmentlevel)));
                if ($player->getInventory()->canAddItem($item)) {
                    $player->getInventory()->addItem($item);
                }
                break;
            case BlockTypeIds::CACTUS:
                $item = VanillaBlocks::CACTUS()->asItem()->setCount(ceil(3 + 0.5 * $enchantmentlevel) * (2 ? $block->getSide(Facing::UP) === VanillaBlocks::CACTUS() : 1));
                if ($player->getInventory()->canAddItem($item)) {
                    $player->getInventory()->addItem($item);
                }
                break;
            case BlockTypeIds::NETHER_WART:
                $item = VanillaBlocks::NETHER_WART()->asItem()->setCount(ceil(3 + 0.5 * $enchantmentlevel));
                if ($player->getInventory()->canAddItem($item)) {
                    $player->getInventory()->addItem($item);
                }
                break;
            case BlockTypeIds::POTATOES:
                $item = VanillaItems::POTATO()->setCount(ceil(3 + 0.5 * $enchantmentlevel));
                if ($player->getInventory()->canAddItem($item)) {
                    $player->getInventory()->addItem($item);
                }
                break;
            case BlockTypeIds::CARROTS:
                $item = VanillaItems::CARROT()->setCount(ceil(3 + 0.5 * $enchantmentlevel));
                if ($player->getInventory()->canAddItem($item)) {
                    $player->getInventory()->addItem($item);
                }
                break;
            case BlockTypeIds::PUMPKIN:
                $item = VanillaBlocks::PUMPKIN()->asItem()->setCount(ceil(3 + 0.5 * $enchantmentlevel));
                if ($player->getInventory()->canAddItem($item)) {
                    $player->getInventory()->addItem($item);
                }
                break;
            case BlockTypeIds::MELON:
                $silk = $player->getInventory()->getItemInHand()->getEnchantment(VanillaEnchantments::SILK_TOUCH());
                if ($silk) {
                    $item = VanillaBlocks::MELON()->asItem()->setCount(mt_rand(1, $enchantmentlevel));
                } else {
                    $item = VanillaItems::MELON()->setCount(ceil(5 + 0.375 * $enchantmentlevel));
                }
                if ($player->getInventory()->canAddItem($item)) {
                    $player->getInventory()->addItem($item);
                }
                break;


        }
    }

}