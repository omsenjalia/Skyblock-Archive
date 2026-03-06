<?php

namespace SkyBlock\item;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Event;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use SkyBlock\Main;
use SkyBlock\util\Lore;
use SkyBlock\util\Values;

class ItemManager {

    private static ItemManager $self;

    private $pickaxes;
    private $axes;
    private $shovels;

    private $swords;

    private $armor = [];

    public static array $items = [];

    public function __construct() {
        self::$self = $this;

        foreach (Main::getInstance()->items->get("items", []) as $item) {
            var_dump($item);
        }

        $this->pickaxes = [
            ItemTypeIds::WOODEN_PICKAXE,
            ItemTypeIds::STONE_PICKAXE,
            ItemTypeIds::GOLDEN_PICKAXE,
            ItemTypeIds::IRON_PICKAXE,
            ItemTypeIds::DIAMOND_PICKAXE,
            ItemTypeIds::NETHERITE_PICKAXE
        ];

        $this->axes = [
            ItemTypeIds::WOODEN_AXE,
            ItemTypeIds::STONE_AXE,
            ItemTypeIds::GOLDEN_AXE,
            ItemTypeIds::IRON_AXE,
            ItemTypeIds::DIAMOND_AXE,
            ItemTypeIds::NETHERITE_AXE
        ];

        $this->shovels = [
            ItemTypeIds::WOODEN_SHOVEL,
            ItemTypeIds::STONE_SHOVEL,
            ItemTypeIds::GOLDEN_SHOVEL,
            ItemTypeIds::IRON_SHOVEL,
            ItemTypeIds::DIAMOND_SHOVEL,
            ItemTypeIds::NETHERITE_SHOVEL
        ];

        $this->swords = [
            ItemTypeIds::WOODEN_SWORD,
            ItemTypeIds::STONE_SWORD,
            ItemTypeIds::GOLDEN_SWORD,
            ItemTypeIds::IRON_SWORD,
            ItemTypeIds::DIAMOND_SWORD,
            ItemTypeIds::NETHERITE_SWORD
        ];
    }

    public function isPickaxe(Item $item) : bool {
        if (in_array($item->getTypeId(), $this->pickaxes)) {
            return true;
        }
        return false;
    }

    public function isAxe(Item $item) : bool {
        if (in_array($item->getTypeId(), $this->axes)) {
            return true;
        }
        return false;
    }

    public function isShovel(Item $item) : bool {
        if (in_array($item->getTypeId(), $this->shovels)) {
            return true;
        }
        return false;
    }

    public function isSword(Item $item) : bool {
        if (in_array($item->getTypeId(), $this->swords)) {
            return true;
        }
        return false;
    }

    public static function getInstance() : ItemManager|static {
        return self::$self;
    }

    public function doItemTasks(Item $item, Event $event) {
        if (!$item instanceof Durable) {
            return $item;
        }
        /**Blocks broken lore counter*/
        if (($this->isPickaxe($item) or $this->isShovel($item) or $this->isAxe($item)) and $event instanceof BlockBreakEvent) {
            $count = (int) Lore::getLoreInfo($item->getLore(), Values::BLOCKS_BROKEN_LORE, Lore::BLOCKS_BROKEN_STR);
            Lore::setLoreInfo($item, Values::BLOCKS_BROKEN_LORE, Lore::BLOCKS_BROKEN_STR . number_format(++$count));
            $item->applyDamage(1);
        } else {
            $item->applyDamage(2);
        }

        /***/
        return $item;
    }

}