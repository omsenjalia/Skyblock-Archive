<?php

declare(strict_types=1);

namespace SkyBlock\item;

use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;

class Saddle extends Item {

    public function __construct() {
        parent::__construct(new ItemIdentifier(20153), "Saddle");
        CreativeInventory::getInstance()->add($this);
    }

    public function getMaxStackSize() : int {
        return 1;
    }
}