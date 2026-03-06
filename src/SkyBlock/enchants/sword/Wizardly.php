<?php


namespace SkyBlock\enchants\sword;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use pocketmine\world\particle\HugeExplodeSeedParticle;
use pocketmine\world\Position;

class Wizardly extends BaseMeleeEnchant {

    static int $id = 177;

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
        $this->sendActivation($attacker, "§cStruck by §bWizardly §cEnchant! Health Effects removed");
        $this->sendActivation($player, "§bWizardly §aActivated!");

        $pos = new Position($attacker->getPosition()->getX(), $attacker->getPosition()->getY(), $attacker->getPosition()->getZ(), $attacker->getPosition()->getWorld());
        $pos->getWorld()->addParticle($pos->asVector3(), new HugeExplodeSeedParticle());
        $attacker->knockBack($attacker->getPosition()->x - $player->getPosition()->x, $attacker->getPosition()->z - $player->getPosition()->z, $enchantmentlevel * 0.075);

        $effect = new EffectInstance(VanillaEffects::INVISIBILITY(), $enchantmentlevel * 2 * 20, $this->getLevel($enchantmentlevel), false);
        $player->getEffects()->add($effect);
        $effect = new EffectInstance(VanillaEffects::NAUSEA(), $enchantmentlevel * 2 * 20, $this->getLevel($enchantmentlevel), false);
        $attacker->getEffects()->add($effect);
        $effect = new EffectInstance(VanillaEffects::BLINDNESS(), $enchantmentlevel * 0.5 * 20, ceil($this->getLevel($enchantmentlevel) / 4), false);
        $attacker->getEffects()->add($effect);
        $effect = new EffectInstance(VanillaEffects::WITHER(), $enchantmentlevel * 0.5 * 20, ceil($this->getLevel($enchantmentlevel) / 4), false);
        $attacker->getEffects()->add($effect);
        $effect = new EffectInstance(VanillaEffects::POISON(), $enchantmentlevel * 0.5 * 20, ceil($this->getLevel($enchantmentlevel) / 4), false);
        $attacker->getEffects()->add($effect);
        $effect = new EffectInstance(VanillaEffects::SPEED(), $enchantmentlevel * 2 * 20, ceil($this->getLevel($enchantmentlevel) / 2), false);
        $player->getEffects()->add($effect);
        $effect = new EffectInstance(VanillaEffects::JUMP_BOOST(), $enchantmentlevel * 2 * 20, ceil($this->getLevel($enchantmentlevel) / 2), false);
        $player->getEffects()->add($effect);

        if ($ev instanceof EntityDamageByEntityEvent)
            $ev->setKnockBack(0.2 * ($enchantmentlevel * 0.75));
    }
}
