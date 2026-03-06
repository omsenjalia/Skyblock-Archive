<?php

namespace SkyBlock\block;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeInfo;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\item\ToolTier;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\tiles\AutoSellerTile;

class AutoSeller extends Transparent {

    public function __construct(BlockIdentifier $idInfo) {
        parent::__construct($idInfo, "Auto Seller", new BlockTypeInfo(BlockBreakInfo::pickaxe(3.0, ToolTier::IRON, 9000.0)));
    }

    public function onScheduledUpdate() : void {
        $tile = $this->position->getWorld()->getTile($this->position);
        if ($tile instanceof AutoSellerTile && $tile->onUpdate())
            $this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 1);
    }

    public function onBreak(Item $item, Player $player = null, array &$returnedItems = []) : bool {
        $tile = $this->getPosition()->getWorld()->getTile($this->getPosition());
        if (parent::onBreak($item, $player)) {
            if ($tile instanceof AutoSellerTile) {
                $main = Main::getInstance();
                $island = $main->getIslandManager()->getOnlineIslandByWorld($this->getPosition()->getWorld()->getDisplayName());
                if (!is_null($island)) {
                    if (!is_null($player)) {
                        $player->getInventory()->addItem($main->getEvFunctions()->getAutoSellerBlock($tile->level1, $tile->type));
                    }
                    $island->removeAutoSeller();
                }
            }
            return true;
        }
        return false;
    }

    public function getDrops(Item $item) : array {
        return [VanillaItems::AIR()];
    }
}