<?php


namespace SkyBlock\item;


use pocketmine\item\ItemIdentifier;
use pocketmine\item\ToolTier;

class TieredTool extends \pocketmine\item\TieredTool {
    /** @var ToolTier */
    protected ToolTier $tier;

    public function __construct(ItemIdentifier $identifier, string $name) { // CustomToolTier $tier
        parent::__construct($identifier, $name, \pocketmine\item\ToolTier::DIAMOND());
        $this->tier = ToolTier::NETHERITE();
    }

    public function getMaxDurability() : int {
        return $this->tier->getMaxDurability();
    }

    protected function getBaseMiningEfficiency() : float {
        return $this->tier->getBaseEfficiency();
    }

    public function getFuelTime() : int {
        if ($this->tier->equals(ToolTier::WOOD())) {
            return 200;
        }

        return 0;
    }

    public function getTier() : ToolTier {
        return ToolTier::DIAMOND();
    }
}
