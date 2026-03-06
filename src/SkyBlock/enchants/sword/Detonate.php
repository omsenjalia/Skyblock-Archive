<?php


namespace SkyBlock\enchants\sword;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;

class Detonate extends BaseMeleeEnchant {

    static int $id = 181;

    /**
     * @param Player $holder
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder) : bool {
        return mt_rand(1, 60) === 1;
    }

    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 2)) > 7) ? 8 : $int;
    }

    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $attacker->getEffects()->remove(VanillaEffects::REGENERATION());
        $attacker->getEffects()->remove(VanillaEffects::HEALTH_BOOST());
        $this->sendActivation($attacker, "§cStruck by §bDetonate §cEnchant! Health Effects removed");
        $this->sendActivation($player, "§bDetonate §aActivated!");
        if (!isset($this->pl->notnt[strtolower($player->getName())]))
            $this->pl->notnt[strtolower($player->getName())] = strtolower($player->getName());
        for ($i = 0; $i <= $this->getLevel($enchantmentlevel); $i++) {
            $this->pl->createTNT($attacker->getPosition()->asVector3(), $attacker->getWorld());
        }
    }
}