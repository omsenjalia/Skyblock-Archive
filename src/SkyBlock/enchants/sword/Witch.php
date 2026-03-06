<?php


namespace SkyBlock\enchants\sword;


use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class Witch extends BaseMeleeEnchant {

    static int $id = 142;

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
        $this->sendActivation($attacker, "§cStruck by §bWitch §cEnchant!");
        $this->sendActivation($player, "§bWitch §aActivated!");
        $i = 0;
        $limit = ceil($this->getLevel($enchantmentlevel) / 2.9);
        if ($limit > 6)
            $limit = 6;
        foreach ($attacker->getEffects()->all() as $effect) {
            if ($i < $limit) {
                if (!$effect->getType()->isBad()) {
                    $attacker->getEffects()->remove($effect->getType());
                    $i++;
                }
            }
        }
    }

}