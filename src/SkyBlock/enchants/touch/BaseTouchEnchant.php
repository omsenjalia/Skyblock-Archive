<?php


namespace SkyBlock\enchants\touch;


use pocketmine\block\Block;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;
use SkyBlock\enchants\BaseEnchantment;

abstract class BaseTouchEnchant extends BaseEnchantment {
    abstract public function isApplicableTo(Player $holder, int $enchlevel = 0, Block $block = null) : bool;

    /**
     * @param Player              $player
     * @param PlayerInteractEvent $ev
     * @param int                 $enchantmentlevel
     */
    abstract public function onActivation(Player $player, PlayerInteractEvent $ev, int $enchantmentlevel) : void;
}