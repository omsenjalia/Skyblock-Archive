<?php


namespace SkyBlock\enchants\bow\player;


use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;

class Paralyze extends BaseBowHitPlayerEnchant {

    static int $id = 117;

    /**
     * @param Player                    $attacker
     * @param Player                    $victim
     * @param int                       $enchantmentLevel
     * @param EntityDamageByEntityEvent $event
     */
    public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel, EntityDamageByEntityEvent $event) : void {
        $this->sendActivation($attacker, "§bParalyze §aActivated!");
        $this->sendActivation($victim, "§cStruck by §bParalyze §cEnchant! Health Effects removed");
        $victim->getEffects()->remove(VanillaEffects::REGENERATION());
        $victim->getEffects()->remove(VanillaEffects::HEALTH_BOOST());
        $effect = new EffectInstance(VanillaEffects::WEAKNESS(), $this->getDuration($enchantmentLevel), $this->getLevel($enchantmentLevel), false);
        $victim->getEffects()->add($effect);
        $effect = new EffectInstance(VanillaEffects::BLINDNESS(), $this->getDuration($enchantmentLevel), $this->getLevel($enchantmentLevel), false);
        $victim->getEffects()->add($effect);
    }

}