<?php


namespace SkyBlock\enchants\sword;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use pocketmine\Server;

class Thunderbolt extends BaseMeleeEnchant {

    static int $id = 179;

    public function isApplicableTo(Player $holder) : bool {
        return mt_rand(1, 70) === 1;
    }

    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 2)) > 7) ? 8 : $int;
    }


    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $attacker->getEffects()->remove(VanillaEffects::REGENERATION());
        $attacker->getEffects()->remove(VanillaEffects::HEALTH_BOOST());
        $this->sendActivation($attacker, "§cStruck by §bThunderbolt §cEnchant! Health Effects removed");
        $this->sendActivation($player, "§bThunderbolt §aActivated!");
        $this->pl->strikeLightning($attacker, 5);
        foreach ($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy(5, 5, 5)) as $p) {
            if ($p instanceof Player) {
                $this->pl->strikeLightning($p, 1);
            }
        }
    }

}