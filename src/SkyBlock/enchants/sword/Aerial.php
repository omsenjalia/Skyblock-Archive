<?php


namespace SkyBlock\enchants\sword;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class Aerial extends BaseMeleeEnchant {

    static int $id = 112;

    public function isApplicableTo(Player $holder) : bool {
        return true;
    }

    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 2)) > 7) ? 8 : $int;
    }

    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $pos1 = $attacker->getPosition()->getY();
        $pos2 = $player->getPosition()->getY();
        $hb = $player->getWorld()->getHighestBlockAt($player->getPosition()->getFloorX(), $player->getPosition()->getFloorZ());
        if (($pos2 > $pos1) or (($hb + 2) < $pos2)) {
            $this->sendActivation($player, "§bAerial §aActivated!");
            $this->sendActivation($attacker, "§cStruck by §bAerial §cEnchant!");
            //            $effect = new EffectInstance(VanillaEffects::STRENGTH(), $enchantmentlevel * 2 * 20, $this->getLevel($enchantmentlevel), false);
            //            $player->getEffects()->add($effect);
            $attacker->setHealth($attacker->getHealth() - (($enchantmentlevel - 1) / 100) * 4);
        }
    }
}