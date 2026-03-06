<?php

declare(strict_types=1);

namespace SkyBlock\tiles;

use pocketmine\block\BlockTypeIds;
use pocketmine\block\inventory\FurnaceInventory;
use pocketmine\block\tile\Container;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\NameableTrait;
use pocketmine\block\tile\Spawnable;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Facing;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use pocketmine\world\Position;
use SkyBlock\block\Hopper as HopperBlock;
use SkyBlock\Main;

class Hopper extends Spawnable implements Nameable {

    use NameableTrait;

    public function getSize() : int {
        return 5;
    }

    public function getDefaultName() : string {
        return "Hopper";
    }

    public function getBoundingBox(Position $pos) : AxisAlignedBB {
        return new AxisAlignedBB(
            $pos->x,
            $pos->y,
            $pos->z,
            $pos->x + 1,
            $pos->y + 1,
            $pos->z + 1
        );
    }

    public function onUpdate() : bool {
        if ($this->closed === true) return false;
        if (!($this->getBlock() instanceof HopperBlock)) return false;
        if ((Server::getInstance()->getTick() % 60) == 0) {
            $main = Main::getInstance();
            if (($island = $main->getIslandManager()->getOnlineIslandByWorld($this->getPosition()->getWorld()->getDisplayName())) === null) return false;
            if ($this->getPosition()->getWorld()->getTile($this->getBlock()->getSide(Facing::DOWN)->getPosition()) instanceof Hopper) return false;
            $target = $this->getPosition()->getWorld()->getTile($this->getBlock()->getSide($this->getBlock()->getFacing())->getPosition());
            if (!$target instanceof Container) return false;
            $boundingBox = $this->getBoundingBox($this->getBlock()->getPosition());
            $boundingBox->maxY += round(($boundingBox->maxY + 1), 0, PHP_ROUND_HALF_UP);
            $itemEntities = [];
            $inv = $target->getInventory();
            foreach ($this->getPosition()->getWorld()->getNearbyEntities($boundingBox) as $entity) {
                if (!($entity instanceof ItemEntity) or !$entity->isAlive() or $entity->isFlaggedForDespawn() or $entity->isClosed()) continue;
                $item = $entity->getItem();
                if ($item instanceof Item) {
                    if ($item->isNull()) {
                        $entity->kill();
                        continue;
                    }
                    $targetItem = clone $item;
                    if ($inv->canAddItem($targetItem)) {
                        $entity->flagForDespawn();
                        $itemEntities[] = $targetItem;
                    }
                }
            }
            foreach ($itemEntities as $items) {
                $inv->addItem($items);
            }
            // suck items from container above it
            $bblock = $this->getBlock()->getSide(Facing::UP);
            if ($main->isPrivateChest($bblock, $island->getOwner())) return true;
            $source = $this->getPosition()->getWorld()->getTile($bblock->getPosition());
            if ($source instanceof Container) { // follow vanilla rules
                $inventory = $source->getInventory();
                $firstOccupied = null;
                if ($inventory instanceof FurnaceInventory) {
                    $result = clone $inventory->getResult();
                    if ($result->getTypeId() !== BlockTypeIds::AIR) { // if changed from null
                        if (!$result->isNull()) {
                            $targetItem = clone $result;
                            $inv = $target->getInventory();
                            if ($inv->canAddItem($targetItem)) {
                                $inv->addItem($targetItem);
                                $inventory->removeItem($result);
                            }
                        }
                    }
                } else {
                    for ($index = 0; $index < $inventory->getSize(); $index++) {
                        if (!$inventory->getItem($index)->isNull()) {
                            $firstOccupied = $index;
                            break;
                        }
                    }
                    if ($firstOccupied !== null) { // if changed from null
                        $item = clone $inventory->getItem($firstOccupied);
                        if (!$item->isNull()) {
                            $targetItem = clone $item;
                            $inv = $target->getInventory();
                            if ($inv->canAddItem($targetItem)) {
                                $inv->addItem($targetItem);
                                $inventory->removeItem($item);
                            }
                        }
                    }
                }

            }
        }
        return true;
    }

    public function readSaveData(CompoundTag $nbt) : void {
        //$this->scheduleUpdate();
    }

    protected function writeSaveData(CompoundTag $nbt) : void {
    }
}