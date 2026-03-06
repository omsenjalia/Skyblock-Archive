<?php


namespace SkyBlock\enchants\bow\shoot;


use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\player\Player;
use SkyBlock\enchants\bow\BaseBowEnchant;

abstract class BaseBowShootEnchant extends BaseBowEnchant {
    /**
     * @param Player              $attacker
     * @param int                 $enchantmentLevel
     * @param EntityShootBowEvent $event
     */
    abstract public function onActivation(Player $attacker, int $enchantmentLevel, EntityShootBowEvent $event) : void;
}