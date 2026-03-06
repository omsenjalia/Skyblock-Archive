<?php


namespace SkyBlock\enchants\sword;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use SkyBlock\enchants\BaseEnchantment;

class Disorder extends BaseMeleeEnchant {

    static int $id = 178;

    /**
     * @param Player $holder
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder) : bool {
        return mt_rand(1, 50) === 1;
    }

    /**
     * @param Player            $player
     * @param Player            $attacker
     * @param EntityDamageEvent $ev
     * @param int               $enchantmentlevel
     */
    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $hitem = $attacker->getInventory()->getItemInHand();
        if (!BaseEnchantment::hasEnchantment($hitem, DisorderProtection::$id)) {
            $this->sendActivation($attacker, "§cStruck by §bDisorder §cEnchant!");
            $this->sendActivation($player, "§bDisorder §aActivated!");
            $slot = mt_rand(10, 25);
            $temp = $attacker->getInventory()->getItem($slot);
            $attacker->getInventory()->setItem($slot, $hitem);
            $attacker->getInventory()->setItemInHand($temp);
        } else {
            $this->sendActivation($player, "§bDisorder §cDeactivated! Player has Disorder Protection!");
        }
    }

}