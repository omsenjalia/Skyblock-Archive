<?php


namespace SkyBlock\enchants\bow\shoot;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\player\Player;

class Volley extends BaseBowShootEnchant {

    static int $id = 119;

    /**
     * @param Player              $attacker
     * @param int                 $enchantmentLevel
     * @param EntityShootBowEvent $event
     */
    public function onActivation(Player $attacker, int $enchantmentLevel, EntityShootBowEvent $event) : void {
        $this->sendActivation($attacker, "§bVolley §aActivated!");
        $effect = new EffectInstance(VanillaEffects::STRENGTH(), $enchantmentLevel * 2 * 20, 2, false);
        $attacker->getEffects()->add($effect);
        $attacker->setHealth($attacker->getHealth() + 2);
    }
}