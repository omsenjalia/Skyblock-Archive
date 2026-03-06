<?php

namespace SkyBlock\item\armor;

use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;

class Crown extends Armor {

    public function __construct(ItemIdentifier $identifier, string $name, ArmorTypeInfo $info) {
        parent::__construct($identifier, $name, $info);
    }

}