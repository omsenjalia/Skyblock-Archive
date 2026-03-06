<?php

namespace SkyBlock\spawner;

use pocketmine\entity\Living;

abstract class Creature extends Living {

    public function getXpDropAmount() : int {
        return mt_rand(1, 6);
    }

}
