<?php


namespace SkyBlock\enchants\bow\player;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;
use SkyBlock\enchants\bow\BaseBowEnchant;

abstract class BaseBowHitPlayerEnchant extends BaseBowEnchant {
    /**
     * @param Player                    $attacker
     * @param Player                    $victim
     * @param int                       $enchantmentLevel
     * @param EntityDamageByEntityEvent $event
     */
    abstract public function onActivation(Player $attacker, Player $victim, int $enchantmentLevel, EntityDamageByEntityEvent $event) : void;
}