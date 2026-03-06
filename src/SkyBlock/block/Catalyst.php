<?php

namespace SkyBlock\block;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeInfo;
use pocketmine\block\Opaque;
use pocketmine\item\Item;
use pocketmine\item\ToolTier;
use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\tiles\CatalystTile;

class Catalyst extends Opaque {

    public function __construct(BlockIdentifier $idInfo) {
        parent::__construct($idInfo, "Catalyst", new BlockTypeInfo(BlockBreakInfo::pickaxe(3.0, ToolTier::IRON, 9000.0)));
    }

    public function onScheduledUpdate() : void {
        $tile = $this->position->getWorld()->getTile($this->position);
        if ($tile instanceof CatalystTile && $tile->onUpdate())
            $this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 1); // change delay maybe
    }

    public function onBreak(Item $item, Player $player = null, array &$returnedItems = []) : bool {
        $tile = $this->getPosition()->getWorld()->getTile($this->getPosition());
        if (parent::onBreak($item, $player)) {
            if ($tile instanceof CatalystTile) {
                $main = Main::getInstance();
                $island = $main->getIslandManager()->getOnlineIslandByWorld($this->getPosition()->getWorld()->getDisplayName());
                if (!is_null($island)) {
                    if (!is_null($player)) {
                        $player->getInventory()->addItem($main->getEvFunctions()->getCatalystBlock());
                    }
                    $island->removeOreGen();
                }
            }
            return true;
        }
        return false;
    }
}