<?php


namespace SkyBlock\enchants\sword;


use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class Backstabber extends BaseMeleeEnchant {

    static int $id = 143;

    /**
     * @param Player $holder
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder) : bool {
        return true;
    }

    /**
     * @param Player            $player
     * @param Player            $attacker
     * @param EntityDamageEvent $ev
     * @param int               $enchantmentlevel
     */
    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        if ($player->getDirectionVector()->dot($attacker->getDirectionVector()) > 0) {
            $this->sendActivation($player, "§bBackstabber §aActivated!");
            $this->sendActivation($attacker, "§cStruck by §bBackstabber §cEnchant!");
            $attacker->setHealth($attacker->getHealth() - (($enchantmentlevel - 1) / 100) * 4);

        }
    }
}