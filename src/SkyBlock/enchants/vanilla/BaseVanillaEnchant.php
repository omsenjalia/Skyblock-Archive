<?php

namespace SkyBlock\enchants\vanilla;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use SkyBlock\Main;

abstract class BaseVanillaEnchant extends Enchantment {

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


}