<?php


namespace SkyBlock\enchants\block;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\Axe;
use pocketmine\item\Pickaxe;
use pocketmine\player\Player;

class Devour extends BaseBlockBreakEnchant {

    static int $id = 185;

    /**
     * @param Player $holder
     * @param int    $level
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder, int $level = 0) : bool {
        $heldItem = $holder->getInventory()->getItemInHand();
        if ($heldItem instanceof Axe || $heldItem instanceof \SkyBlock\item\Axe || $heldItem instanceof Pickaxe || $heldItem instanceof \SkyBlock\item\Pickaxe) {
            return mt_rand(1, 5) === 1;
        }
        return false;
    }

    /**
     * @param Player          $player
     * @param BlockBreakEvent $ev
     * @param int             $enchantmentlevel
     */
    public function onActivation(Player $player, BlockBreakEvent $ev, int $enchantmentlevel) : void {
        $player->getHungerManager()->addFood($enchantmentlevel * 1.3333);
    }

}