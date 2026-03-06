<?php


namespace SkyBlock\enchants\sword;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use pocketmine\world\particle\EntityFlameParticle;
use pocketmine\world\Position;

class Brawler extends BaseMeleeEnchant {

    static int $id = 176;

    /**
     * @param Player $holder
     *
     * @return bool
     */
    public function isApplicableTo(Player $holder) : bool {
        return mt_rand(1, 50) === 1;
    }

    public function getLevel(int $level = 1) : int {
        return (($int = ceil($level / 2)) > 7) ? 8 : $int;
    }

    public function onActivation(Player $player, Player $attacker, EntityDamageEvent $ev, int $enchantmentlevel) : void {
        $attacker->getEffects()->remove(VanillaEffects::REGENERATION());
        $attacker->getEffects()->remove(VanillaEffects::HEALTH_BOOST());
        $this->sendActivation($attacker, "§cStruck by §bBrawler §cEnchant! Health Effects removed");
        $this->sendActivation($player, "§bBrawler §aActivated!");

        $pos = new Position($attacker->getPosition()->getX(), $attacker->getPosition()->getY(), $attacker->getPosition()->getZ(), $attacker->getPosition()->getWorld());
        $particle = new EntityFlameParticle();
        $pos->getWorld()->addParticle($pos, $particle);

        $attacker->knockBack($attacker->getPosition()->getX() - $player->getPosition()->x, $attacker->getPosition()->z - $player->getPosition()->z, $enchantmentlevel * 0.075);
        $attacker->setOnFire((int) (($enchantmentlevel / 2) * 1.5));

        $effect = new EffectInstance(VanillaEffects::HUNGER(), $enchantmentlevel * 2 * 20, $this->getLevel($enchantmentlevel), false);
        $attacker->getEffects()->add($effect);
        $effect = new EffectInstance(VanillaEffects::WEAKNESS(), $enchantmentlevel * 2 * 20, $this->getLevel($enchantmentlevel), false);
        $attacker->getEffects()->add($effect);

        if ($ev instanceof EntityDamageByEntityEvent)
            $ev->setKnockBack(0.2 * ($enchantmentlevel * 0.75));
    }
}