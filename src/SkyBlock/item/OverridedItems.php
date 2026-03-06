<?php

declare(strict_types=1);

namespace SkyBlock\item;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\ToolTier;
use pocketmine\item\VanillaItems;
use pocketmine\utils\CloningRegistryTrait;
use function str_replace;

final class OverridedItems {
    use CloningRegistryTrait;

    protected static function setup() : void {
        //        self::register(new Key(new ItemIdentifier(1001,0),"§r§6Vote Key\n§r§fUse this key at /warp crates"),true);
        //        self::register(new Key(new ItemIdentifier(1002,0),"§r§aCommon Key\n§r§fUse this key at /warp crates"),true);
        //        self::register(new Key(new ItemIdentifier(1003,0),"§r§bRare Key\n§r§fUse this key at /warp crates"),true);
        //        self::register(new Key(new ItemIdentifier(1004,0),"§r§e§lLegendary Key\n§r§fUse this key at /warp crates"),true);
        //        self::register(new Key(new ItemIdentifier(1005,0),"§r§d§lMystic Key\n§r§fUse this key at /warp crates"),true);
        //        self::register(new CustomBook(new ItemIdentifier(1011,0),"CEBook"),true);
        //        self::register(new CustomBook(new ItemIdentifier(1012,0),"CEBook"),true);
        //        self::register(new CustomBook(new ItemIdentifier(1013,0),"CEBook"),true);
        //        self::register(new CustomBook(new ItemIdentifier(1014,0),"CEBook"),true);
        //        self::register(new CustomBook(new ItemIdentifier(1015,0),"CEBook"),true);
        self::register(new DAxe(new ItemIdentifier(ItemTypeIds::DIAMOND_AXE), "Diamond Axe", ToolTier::DIAMOND()), true);
        self::register(new DAxe(new ItemIdentifier(ItemTypeIds::GOLDEN_AXE), "Golden Axe", ToolTier::GOLD()), true);
        self::register(new DAxe(new ItemIdentifier(ItemTypeIds::IRON_AXE), "Iron Axe", ToolTier::IRON()), true);
        self::register(new DAxe(new ItemIdentifier(ItemTypeIds::STONE_AXE), "Stone Axe", ToolTier::STONE()), true);
        self::register(new DAxe(new ItemIdentifier(ItemTypeIds::WOODEN_AXE), "Wooden Axe", ToolTier::WOOD()), true);
        self::register(new DPickaxe(new ItemIdentifier(ItemTypeIds::DIAMOND_PICKAXE), "Diamond Pickaxe", ToolTier::DIAMOND()), true);
        self::register(new DPickaxe(new ItemIdentifier(ItemTypeIds::GOLDEN_PICKAXE), "Golden Pickaxe", ToolTier::GOLD()), true);
        self::register(new DPickaxe(new ItemIdentifier(ItemTypeIds::IRON_PICKAXE), "Iron Pickaxe", ToolTier::IRON()), true);
        self::register(new DPickaxe(new ItemIdentifier(ItemTypeIds::STONE_PICKAXE), "Stone Pickaxe", ToolTier::STONE()), true);
        self::register(new DPickaxe(new ItemIdentifier(ItemTypeIds::WOODEN_PICKAXE), "Wooden Pickaxe", ToolTier::WOOD()), true);
        self::register(new DShovel(new ItemIdentifier(ItemTypeIds::DIAMOND_SHOVEL), "Diamond Shovel", ToolTier::DIAMOND()), true);
        self::register(new DShovel(new ItemIdentifier(ItemTypeIds::GOLDEN_SHOVEL), "Golden Shovel", ToolTier::GOLD()), true);
        self::register(new DShovel(new ItemIdentifier(ItemTypeIds::IRON_SHOVEL), "Iron Shovel", ToolTier::IRON()), true);
        self::register(new DShovel(new ItemIdentifier(ItemTypeIds::STONE_SHOVEL), "Stone Shovel", ToolTier::STONE()), true);
        self::register(new DShovel(new ItemIdentifier(ItemTypeIds::WOODEN_SHOVEL), "Wooden Shovel", ToolTier::WOOD()), true);
        self::register(new DSword(new ItemIdentifier(ItemTypeIds::DIAMOND_SWORD), "Diamond Sword", ToolTier::DIAMOND()), true);
        self::register(new DSword(new ItemIdentifier(ItemTypeIds::GOLDEN_SWORD), "Golden Sword", ToolTier::GOLD()), true);
        self::register(new DSword(new ItemIdentifier(ItemTypeIds::IRON_SWORD), "Iron Sword", ToolTier::IRON()), true);
        self::register(new DSword(new ItemIdentifier(ItemTypeIds::STONE_SWORD), "Stone Sword", ToolTier::STONE()), true);
        self::register(new DSword(new ItemIdentifier(ItemTypeIds::WOODEN_SWORD), "Wooden Sword", ToolTier::WOOD()), true);
        //        $NPickaxe = new NPickaxe(new ItemIdentifier(745,0),"Netherite Pickaxe",new CustomToolTier("Netherite",9 ,2031,9,10));
        $item = VanillaItems::DIAMOND_CHESTPLATE();
        //        $item->getNamedTag()->setTag(Main::TAG_TRIM);
    }

    protected static function register(Item $item, bool $overrideCoreItem = true) : void {
        $name = str_replace(" ", "_", strtolower($item->getName()));
        self::_registryRegister($name, $item);
        if ($overrideCoreItem) {
            // TODO
        }
    }
}