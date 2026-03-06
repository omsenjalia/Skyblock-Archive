<?php


namespace SkyBlock\item;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;

class key extends Item {
    private $type;

    public function __construct(ItemIdentifier $identifier, string $name = "Unknown", string $type = 'vote') {
        $this->type = $type;
        $this->setCustomName($name);
        parent::__construct($identifier, $name);
    }

    public function getType() {
        return $this->type;
    }
}