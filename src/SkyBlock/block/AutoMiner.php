<?php


namespace SkyBlock\block;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeInfo;
use pocketmine\block\Opaque;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\Item;
use pocketmine\item\ToolTier;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\tiles\AutoMinerTile;

class AutoMiner extends Opaque {

    public function __construct(BlockIdentifier $idInfo) {
        parent::__construct($idInfo, "Auto Miner", new BlockTypeInfo(BlockBreakInfo::pickaxe(3.0, ToolTier::IRON, 9000.0)));
    }

    public function onScheduledUpdate() : void {
        $tile = $this->position->getWorld()->getTile($this->position);
        if ($tile instanceof AutoMinerTile && $tile->onUpdate())
            $this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 1);
    }

    public function onEntityLand(Entity $entity) : ?float {
        if ($entity instanceof Living && $entity->isSneaking()) {
            return null;
        }
        $entity->resetFallDistance();
        return -$entity->getMotion()->getY();
    }

    public function onBreak(Item $item, Player $player = null, array &$returnedItems = []) : bool {
        $tile = $this->getPosition()->getWorld()->getTile($this->getPosition());
        if (parent::onBreak($item, $player)) {
            if ($tile instanceof AutoMinerTile) {
                $main = Main::getInstance();
                $island = $main->getIslandManager()->getOnlineIslandByWorld($this->getPosition()->getWorld()->getDisplayName());
                if (!is_null($island)) {
                    if (!is_null($player)) {
                        $player->getInventory()->addItem($main->getEvFunctions()->getAutoMinerBlock($tile->level1, $tile->fortune, $tile->fortune));
                    }
                    $island->removeAutoMiner();
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