<?php


namespace SkyBlock\enchants\block;


use pocketmine\block\BlockToolType;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\Pickaxe;
use pocketmine\player\Player;

class Booster extends BaseBlockBreakEnchant {

    static int $id = 139;

    public function isApplicableTo(Player $holder, int $level = 0) : bool {
        if ($holder->getInventory()->getItemInHand() instanceof Pickaxe or $holder->getInventory()->getItemInHand() instanceof \SkyBlock\item\Pickaxe) {
            return mt_rand(1, 20) <= $level;
        }
        return false;
    }

    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 2)) > 7) ? 7 : $int;
    }


    public function onActivation(Player $player, BlockBreakEvent $ev, int $enchantmentlevel) : void {
        if ($ev->getBlock()->getBreakInfo()->getToolType() === BlockToolType::PICKAXE) {
            $effect = new EffectInstance(VanillaEffects::SPEED(), $enchantmentlevel * 2 * 20, $this->getLevel($enchantmentlevel), false);
            $player->getEffects()->add($effect);
        }
    }
}