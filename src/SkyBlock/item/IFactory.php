<?php


namespace SkyBlock\item;


use alvin0319\CustomItemLoader\block\BlockIds;
use alvin0319\CustomItemLoader\CItem;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\utils\CloningRegistryTrait;
use SkyBlock\Main;

/**
 * @generate-registry-docblock
 * @method static Item NETHERITE_SCRAP()
 * @method static Item NETHERITE_INGOT()
 * @method static Item ANCIENT_DEBRIS()
 * @method static Item NETHERITE_BLOCK()
 */
class IFactory {
    use CloningRegistryTrait;


    /** @var string[] */
    public const ITEM_CLASSES
        = [
            Saddle::class,
        ];
    /** @var Main $pl */
    private Main $pl;

    private ItemManager $im;

    public function __construct(Main $plugin) {
        $this->pl = $plugin;
        $this->im = new ItemManager();
    }

    public function init() {
        //        $this->register(new Key(new ItemIdentifier(1001,0),"§r§6Vote Key\n§r§fUse this key at /warp crates"),true);
        //        $this->register(new Key(new ItemIdentifier(1002,0),"§r§aCommon Key\n§r§fUse this key at /warp crates"),true);
        //        $this->register(new Key(new ItemIdentifier(1003,0),"§r§bRare Key\n§r§fUse this key at /warp crates"),true);
        //        $this->register(new Key(new ItemIdentifier(1004,0),"§r§e§lLegendary Key\n§r§fUse this key at /warp crates"),true);
        //        $this->register(new Key(new ItemIdentifier(1005,0),"§r§d§lMystic Key\n§r§fUse this key at /warp crates"),true);
        //        $this->register(new CustomBook(new ItemIdentifier(1011,0),"CEBook"),true);
        //        $this->register(new CustomBook(new ItemIdentifier(1012,0),"CEBook"),true);
        //        $this->register(new CustomBook(new ItemIdentifier(1013,0),"CEBook"),true);
        //        $this->register(new CustomBook(new ItemIdentifier(1014,0),"CEBook"),true);
        //        $this->register(new CustomBook(new ItemIdentifier(1015,0),"CEBook"),true);
        //        $this->register(new DAxe(new ItemIdentifier(ItemTypeIds::DIAMOND_AXE, 0), "Diamond Axe", ToolTier::DIAMOND()), true);
        //        $this->register(new DAxe(new ItemIdentifier(ItemTypeIds::GOLDEN_AXE, 0), "Golden Axe", ToolTier::GOLD()), true);
        //        $this->register(new DAxe(new ItemIdentifier(ItemTypeIds::IRON_AXE, 0), "Iron Axe", ToolTier::IRON()), true);
        //        $this->register(new DAxe(new ItemIdentifier(ItemTypeIds::STONE_AXE, 0), "Stone Axe", ToolTier::STONE()), true);
        //        $this->register(new DAxe(new ItemIdentifier(ItemTypeIds::WOODEN_AXE, 0), "Wooden Axe", ToolTier::WOOD()), true);
        //        $this->register(new DPickaxe(new ItemIdentifier(ItemTypeIds::DIAMOND_PICKAXE, 0), "Diamond Pickaxe", ToolTier::DIAMOND()), true);
        //        $this->register(new DPickaxe(new ItemIdentifier(ItemTypeIds::GOLDEN_PICKAXE, 0), "Golden Pickaxe", ToolTier::GOLD()), true);
        //        $this->register(new DPickaxe(new ItemIdentifier(ItemTypeIds::IRON_PICKAXE, 0), "Iron Pickaxe", ToolTier::IRON()), true);
        //        $this->register(new DPickaxe(new ItemIdentifier(ItemTypeIds::STONE_PICKAXE, 0), "Stone Pickaxe", ToolTier::STONE()), true);
        //        $this->register(new DPickaxe(new ItemIdentifier(ItemTypeIds::WOODEN_PICKAXE, 0), "Wooden Pickaxe", ToolTier::WOOD()), true);
        //        $this->register(new DShovel(new ItemIdentifier(ItemTypeIds::DIAMOND_SHOVEL, 0), "Diamond Shovel", ToolTier::DIAMOND()), true);
        //        $this->register(new DShovel(new ItemIdentifier(ItemTypeIds::GOLDEN_SHOVEL, 0), "Golden Shovel", ToolTier::GOLD()), true);
        //        $this->register(new DShovel(new ItemIdentifier(ItemTypeIds::IRON_SHOVEL, 0), "Iron Shovel", ToolTier::IRON()), true);
        //        $this->register(new DShovel(new ItemIdentifier(ItemTypeIds::STONE_SHOVEL, 0), "Stone Shovel", ToolTier::STONE()), true);
        //        $this->register(new DShovel(new ItemIdentifier(ItemTypeIds::WOODEN_SHOVEL, 0), "Wooden Shovel", ToolTier::WOOD()), true);
        //        $this->register(new DSword(new ItemIdentifier(ItemTypeIds::DIAMOND_SWORD, 0), "Diamond Sword", ToolTier::DIAMOND()), true);
        //        $this->register(new DSword(new ItemIdentifier(ItemTypeIds::GOLDEN_SWORD, 0), "Golden Sword", ToolTier::GOLD()), true);
        //        $this->register(new DSword(new ItemIdentifier(ItemTypeIds::IRON_SWORD, 0), "Iron Sword", ToolTier::IRON()), true);
        //        $this->register(new DSword(new ItemIdentifier(ItemTypeIds::STONE_SWORD, 0), "Stone Sword", ToolTier::STONE()), true);
        //        $this->register(new DSword(new ItemIdentifier(ItemTypeIds::WOODEN_SWORD, 0), "Wooden Sword", ToolTier::WOOD()), true);


        //        $NPickaxe = new NPickaxe(new ItemIdentifier(745,0),"Netherite Pickaxe",new CustomToolTier("Netherite",9 ,2031,9,10));
        //        $this->register($NPickaxe,true);
        //        CreativeInventory::getInstance()->add($NPickaxe);
        //        $NAxe = new NAxe(new ItemIdentifier(746,0),"Netherite Axe",new CustomToolTier("Netherite",6 ,2031,9,10));
        //        $this->register($NAxe,true);
        //        CreativeInventory::getInstance()->add($NAxe);
        //        $NShovel = new NShovel(new ItemIdentifier(744,0),"Netherite Shovel",new CustomToolTier("Netherite",6 ,2031,9,10));
        //        $this->register($NShovel,true);
        //        CreativeInventory::getInstance()->add($NShovel);
        //        $this->register(new Armor(new ItemIdentifier(ItemTypeIds::TURTLE_HELMET, 0), "Koth Crown", new ArmorTypeInfo(3,364, ArmorInventory::SLOT_HEAD)),"Koth Crown");
        //
        //        /**Tools*/////////////////////////////////////////
        //
        //        $BloodMask = new BloodDragon(new ItemIdentifier(2001,0));
        //        $this->register($BloodMask,true);
        //        CreativeInventory::getInstance()->add($BloodMask);
        //
        //        $WardenMask = new Warden(new ItemIdentifier(2002,0));
        //        $this->register($WardenMask,true);
        //        CreativeInventory::getInstance()->add($WardenMask);
        //
        //        $EnderDragon = new EnderDragon(new ItemIdentifier(2003,0));
        //        $this->register($EnderDragon,true);
        //        CreativeInventory::getInstance()->add($EnderDragon);
        //
        //        $Enderman = new Enderman(new ItemIdentifier(2004,0));
        //        $this->register($Enderman,true);
        //        CreativeInventory::getInstance()->add($Enderman);
        //
        //        //////////////////////////////////////////////////
        //
        //
        //        /**Items*/////////////////////////////////////////
        //        $stip = new StringToItemParser();
        //        $stip->register("netherite_scrap",fn() => IFactory::NETHERITE_SCRAP());
        //        $stip->register("netherite_ingot",fn() => IFactory::NETHERITE_INGOT());
        //        $stip->register("ancient_debris",fn() => IFactory::ANCIENT_DEBRIS());
        //        $stip->register("netherite_block",fn() => IFactory::Netherite_BLOCK());
        //
        //        //////////////////////////////////////////////////
        //
        //
        //
        //        $this->pl->getServer()->getLogger()->info("§f=> §eRegistered §6" . count(self::ITEM_CLASSES) . " §eitems! §f<=");
    }

    protected static function setup() : void {
        //        self::fullItemRegister(new Item(new ItemIdentifier(752,0),""),"netherite_scrap");
        //        self::fullItemRegister(new Item(new ItemIdentifier(742,0),""), "netherite_ingot");
        //
        //        $blockItem = VanillaBlocks::ANCIENT_DEBRIS()->asItem();
        //        $blockItem->setLore(["§jThis block can only be smelted in a Blast Furnace."]);
        //        self::register($blockItem,true);
        //        self::fullItemRegister($blockItem,"ancient_debris");
        //
        //        $blockItem = VanillaBlocks::NETHERITE()->asItem();
        //        self::register($blockItem,true);
        //        self::_registryRegister("netherite_block",$blockItem);

    }

    private static function fullItemRegister(Item $item, string $alias) {
        CreativeInventory::getInstance()->add($item);
        self::_registryRegister($alias, $item);
    }

    private static function register(object $member, string $name) : void {
        //TODO Overriding not supported anymore so idk this dumb
    }


}