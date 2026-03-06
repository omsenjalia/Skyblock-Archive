<?php

namespace SkyBlock\enchants\vanilla;

use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\MeleeWeaponEnchantment;
use SkyBlock\Main;

class Knockback extends MeleeWeaponEnchantment {

    /** @var Main $pl */
    public Main $pl;

    /** @var int */
    public int $id;

    /**
     * @param Main   $plugin
     * @param int    $id
     * @param string $name
     */
    public function __construct(Main $plugin, int $id, string $name) {
        $this->pl = $plugin;
        $this->id = $id;
        parent::__construct($name, 3, ItemFlags::ALL, ItemFlags::ALL, 10);
    }

    public function isApplicableTo(Entity $victim) : bool {
        return $victim instanceof Living;
    }

    public function getDamageBonus(int $enchantmentLevel) : float {
        return 0;
    }

    public function onPostAttack(Entity $attacker, Entity $victim, int $enchantmentLevel) : void {
        if ($victim instanceof Living) {
            $max = 8;
            $enchantmentLevel = ($enchantmentLevel > $max) ? $max : $enchantmentLevel;
            $factor = ($enchantmentLevel * 0.012) + 0.382;
            $diff = $victim->getPosition()->subtractVector($attacker->getPosition());
            $victim->knockBack($diff->x, $diff->z, $factor);
        }
    }

}