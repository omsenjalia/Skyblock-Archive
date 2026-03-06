<?php

declare(strict_types=1);

namespace SkyBlock\block;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeInfo;
use pocketmine\block\Hopper as PMHopper;
use pocketmine\item\Item;
use pocketmine\item\ToolTier;
use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\tiles\Hopper as TileHopper;

class Hopper extends PMHopper {

    public function __construct(BlockIdentifier $id) {
        parent::__construct($id, "Hopper", new BlockTypeInfo(BlockBreakInfo::pickaxe(3.0, ToolTier::WOOD, 15.0)));
    }

    public function onNearbyBlockChange() : void {
        $this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 1);
    }

    public function onScheduledUpdate() : void {
        $tile = $this->position->getWorld()->getTile($this->position);
        if ($tile instanceof TileHopper && $tile->onUpdate())
            $this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 1);
    }

    public function onBreak(Item $item, Player $player = null, array &$returnedItems = []) : bool {
        if (parent::onBreak($item, $player)) {
            $main = Main::getInstance();
            $island = $main->getIslandManager()->getOnlineIslandByWorld($this->getPosition()->getWorld()->getDisplayName());
            if (!is_null($island)) $island->removeHopper();
            return true;
        }
        return false;
    }

}