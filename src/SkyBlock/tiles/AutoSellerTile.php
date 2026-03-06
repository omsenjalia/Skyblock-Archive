<?php

namespace SkyBlock\tiles;

use pocketmine\block\tile\Chest;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\NameableTrait;
use pocketmine\block\tile\Spawnable;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\Server;
use SkyBlock\block\AutoSeller;
use SkyBlock\Main;

class AutoSellerTile extends Spawnable implements Nameable {

    use NameableTrait;

    public const TAG_LEVEL = "Level";
    public const TAG_TYPE_XP = 1;
    public const TAG_TYPE_MONEY = 0;
    public const MAX_LEVEL = 8;
    public const TAG_TYPE = "Type";
    public int $delay = 200, $level1 = 0, $type = 0;

    public static function getTypeName(int $type) : string {
        return ($type === self::TAG_TYPE_MONEY) ? "money" : "xp";
    }

    public function onUpdate() : bool {
        if ($this->level1 === 0) $this->close();
        if ($this->closed) return false;
        $block = $this->getBlock();
        if (!$block instanceof AutoSeller) return false;
        $inst = Main::getInstance();
        if (($island = $inst->getIslandManager()->getOnlineIslandByWorld($this->getPosition()->getWorld()->getDisplayName())) === null) return false;
        if (!isset($inst->autoseller[$this->getPosition()->getWorld()->getDisplayName()])) {
            if ($this->delay <= 0) {
                $this->delay = $this->getDelayByLevel($this->level1);
                $down = $this->getPosition()->getWorld()->getTile($this->getBlock()->getSide(Facing::DOWN)->getPosition());
                if ($down instanceof Chest) {
                    $inv = $down->getInventory();
                    foreach ($inv->getContents() as $item) {
                        if ($item instanceof Item) {
                            //                                var_dump(Main::getInstance()->getEvFunctions()->getSellMoneyData($item->getVanillaName()));
                            if (Main::getInstance()->getEvFunctions()->getSellMoneyData($item->getVanillaName()) !== 0) {
                                $receiver = $island->getReceiver();
                                $player = Server::getInstance()->getPlayerExact($receiver);
                                if (!$player instanceof Player) {
                                    if (($player = $island->getRandomOnlineCoOwner()) === null) return true;
                                    $receiver = $player->getName();
                                }
                                if (($user = $inst->getUserManager()->getOnlineUser($receiver)) !== null && $island->isAnOwner($receiver)) {
                                    if ($this->type === self::TAG_TYPE_MONEY)
                                        $inst->getEvFunctions()->sellItem($user, $item->setCount($this->level1), "money");
                                    else if ($this->type === self::TAG_TYPE_XP)
                                        $inst->getEvFunctions()->sellItem($user, $item->setCount($this->level1), "xp");
                                    $inv->removeItem($item);
                                    return true;
                                }
                            }
                        }
                    }
                }
            } else {
                --$this->delay;
            }
        }
        return true;
    }

    public function getDelayByLevel(int $level) : int {
        if ($level == 1) return 15 * 20; // 15 seconds
        elseif ($level == 2) return 10 * 20;// 10 seconds
        elseif ($level == 3) return 8 * 20;// 8 seconds
        elseif ($level == 4) return 6 * 20;// 6 seconds
        elseif ($level == 5) return 4 * 20;// 4 seconds
        elseif ($level == 6) return 2 * 20;// 2 seconds
        elseif ($level == 7) return 30; // 1.5 second
        elseif ($level == 8) return 20; // 1 a second
        else return 200;
    }

    public function getName() : string {
        return "AutoSeller";
    }

    public function getDefaultName() : string {
        return "AutoSeller";
    }

    public function readSaveData(CompoundTag $nbt) : void {
        if ($nbt->getInt(self::TAG_LEVEL, null) === null) $this->close();

        $this->level1 = $nbt->getInt(self::TAG_LEVEL, $this->level1);
        $this->type = $nbt->getInt(self::TAG_TYPE, $this->type);

        $this->delay = $this->getDelayByLevel($this->level1);
    }

    public function getDelayInSeconds() : int {
        return (int) ($this->getDelayByLevel($this->level1) / 20);
    }

    public function setDelay(int $delay) : void {
        $this->delay = $delay;
    }

    protected function writeSaveData(CompoundTag $nbt) : void {
        $this->applyBaseNBT($nbt);
    }

    private function applyBaseNBT(CompoundTag $nbt) : void {
        $nbt->setInt(self::TAG_LEVEL, $this->level1);
        $nbt->setInt(self::TAG_TYPE, $this->type);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt) : void {
        $this->applyBaseNBT($nbt);
    }


}
