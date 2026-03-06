<?php


namespace SkyBlock\block;


use customiesdevs\customies\block\CustomiesBlockFactory;
use pocketmine\block\Block;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeInfo;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\block\tile\Tile;
use pocketmine\block\VanillaBlocks;
use pocketmine\crafting\CraftingManagerFromDataHelper;
use pocketmine\data\bedrock\item\ItemTypeNames;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\inventory\CreativeInventory;
use pocketmine\Server;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use SkyBlock\block\ores\NetherrackBlock;
use SkyBlock\Main;
use SkyBlock\tiles\AutoMinerTile;
use SkyBlock\tiles\AutoSellerTile;
use SkyBlock\tiles\CatalystTile;
use SkyBlock\tiles\MobSpawner;
use SkyBlock\tiles\OreGenTile;
use Symfony\Component\Filesystem\Path;
use function is_subclass_of;

class BFactory {

    /** @var Main $pl */
    public Main $pl;

    public function __construct(Main $plugin) {
        $this->pl = $plugin;
    }

    public function init() : void {

        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::PURPLE_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "Purple Glazed Terracotta"), true);
        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::WHITE_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "White Glazed Terracotta"), true);
        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::ORANGE_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "Orange Glazed Terracotta"), true);
        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::MAGENTA_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "Magenta Glazed Terracotta"), true);
        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::LIGHT_BLUE_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "Light Blue Glazed Terracotta"), true);
        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::YELLOW_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "Yellow Glazed Terracotta"), true);
        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::LIME_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "Lime Glazed Terracotta"), true);
        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::PINK_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "Pink Glazed Terracotta"), true);
        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::GRAY_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "Grey Glazed Terracotta"), true);
        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::SILVER_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "Light Grey Glazed Terracotta"), true);
        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::CYAN_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "Cyan Glazed Terracotta"), true);
        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::BLUE_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "Blue Glazed Terracotta"), true);
        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::BROWN_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "Brown Glazed Terracotta"), true);
        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::GREEN_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "Green Glazed Terracotta"), true);
        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::RED_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "Red Glazed Terracotta"), true);
        //        BlockFactory::getInstance()->register(new GlazedTerracotta(new BlockIdentifier(BlockLegacyIds::BLACK_GLAZED_TERRACOTTA, 0, null, OreGenTile::class), "Black Glazed Terracotta"), true);
        //
        //        BlockFactory::getInstance()->register(new DeepslateOre(new BlockIdentifier(BlockTypeIds::DEEPSLATE_COAL_ORE,0,null),"Deepslate Coal Ore", new BlockBreakInfo(3.8,BlockToolType::PICKAXE,5), BlockFactory::getInstance()->get(BlockLegacyIds::COAL_ORE, 0)),true);
        //        BlockFactory::getInstance()->register(new DeepslateOre(new BlockIdentifier(BlockTypeIds::DEEPSLATE_IRON_ORE,0,null),"Deepslate Iron Ore", new BlockBreakInfo(3.8,BlockToolType::PICKAXE,5), BlockFactory::getInstance()->get(BlockLegacyIds::IRON_ORE, 0)),true);
        //        BlockFactory::getInstance()->register(new DeepslateOre(new BlockIdentifier(BlockTypeIds::DEEPSLATE_GOLD_ORE,0,null),"Deepslate Gold Ore", new BlockBreakInfo(3.8,BlockToolType::PICKAXE,5), BlockFactory::getInstance()->get(BlockLegacyIds::GOLD_ORE, 0)),true);
        //        BlockFactory::getInstance()->register(new DeepslateOre(new BlockIdentifier(BlockTypeIds::DEEPSLATE_LAPIS_ORE,0,null),"Deepslate Lapis Ore", new BlockBreakInfo(3.8,BlockToolType::PICKAXE,5), BlockFactory::getInstance()->get(BlockLegacyIds::LAPIS_ORE, 0)),true);
        //        BlockFactory::getInstance()->register(new DeepslateOre(new BlockIdentifier(BlockTypeIds::DEEPSLATE_DIAMOND_ORE,0,null),"Deepslate Diamond Ore", new BlockBreakInfo(3.8,BlockToolType::PICKAXE,5), BlockFactory::getInstance()->get(BlockLegacyIds::DIAMOND_ORE, 0)),true);
        //        BlockFactory::getInstance()->register(new DeepslateOre(new BlockIdentifier(BlockTypeIds::DEEPSLATE_EMERALD_ORE,0,null),"Deepslate Emerald Ore", new BlockBreakInfo(3.8,BlockToolType::PICKAXE,5), BlockFactory::getInstance()->get(BlockLegacyIds::EMERALD_ORE, 0)),true);

        //        $oreGenTypeId = BlockTypeIds::newId();
        //        $material = new Material(Material::RENDER_METHOD_OPAQUE, "oregen", Material::RENDER_METHOD_OPAQUE);
        //        $model = new Model([$material], "geometry.oregen", new Vector3(-8, 0, -8), new Vector3(16, 16, 16));
        //        CustomiesBlockFactory::getInstance()->registerBlock(static function() use ($oreGenTypeId) : Block{
        //            return new OreGenBlock(new BlockIdentifier($oreGenTypeId, OreGenTile::class), "Ore Generator");
        //        }, "fallentech:oregen", $model, CreativeInventoryInfo::DEFAULT());
        //
        //        $autoSellerTypeId = BlockTypeIds::newId();
        //        $material = new Material(Material::RENDER_METHOD_OPAQUE, "smithing_table_bottom", Material::RENDER_METHOD_OPAQUE);
        //        $model = new Model([$material], "geometry.oregen", new Vector3(-8, 0, -8), new Vector3(16, 16, 16));
        //        CustomiesBlockFactory::getInstance()->registerBlock(static function() use ($autoSellerTypeId) : Block{
        //            return new AutoSeller(new BlockIdentifier($autoSellerTypeId, AutoSellerTile::class));
        //        }, "fallentech:autoseller", $model, CreativeInventoryInfo::DEFAULT());
        //
        //        $autoMinerTypeId = BlockTypeIds::newId();
        //        $material = new Material(Material::RENDER_METHOD_OPAQUE, "beacon"/*"autominer"*/, Material::RENDER_METHOD_OPAQUE);
        //        $model = new Model([$material], "geometry.oregen", new Vector3(-8, 0, -8), new Vector3(16, 16, 16));
        //        CustomiesBlockFactory::getInstance()->registerBlock(static function() use ($autoMinerTypeId) : Block{
        //            return new AutoMiner(new BlockIdentifier($autoMinerTypeId, AutoMinerTile::class));
        //        }, "fallentech:autominer", $model, CreativeInventoryInfo::DEFAULT());

        $this->overrideBlock(AutoSeller::class, "Auto Seller", "autoseller", CustomiesBlockFactory::getInstance()->get("fallentech:autoseller"), "fallentech:autoseller", AutoSellerTile::class);
        $this->overrideBlock(AutoMiner::class, "Auto Miner", "autominer", CustomiesBlockFactory::getInstance()->get("fallentech:autominer"), "fallentech:autominer", AutoMinerTile::class);
        $this->overrideBlock(Catalyst::class, "Catalyst", "catalyst", CustomiesBlockFactory::getInstance()->get("fallentech:catalyst"), "fallentech:catalyst", CatalystTile::class);

        $this->overrideBlock(Hopper::class, "Hopper", "hopper", VanillaBlocks::HOPPER(), ItemTypeNames::HOPPER, \SkyBlock\tiles\Hopper::class);
        $this->overrideBlock(MonsterSpawner::class, "Monster Spawner", "monster_spawner", VanillaBlocks::MONSTER_SPAWNER(), "minecraft:mob_spawner", MobSpawner::class);

        $this->finalizeOverride();
    }

    /**
     * @phpstan-param class-string<Block>     $blockClass
     * @phpstan-param class-string<Tile>|null $tileClass
     */
    private function overrideBlock(string $blockClass, string $name, string $enumName, Block $oldBlock, string $typename, ?string $tileClass = null) : void {
        if (!is_subclass_of($blockClass, Block::class)) {
            throw new \InvalidArgumentException("Cannot register non-block class $blockClass");
        }
        $block = new $blockClass(
            new BlockIdentifier($oldBlock->getTypeId(), $tileClass),
            $name,
            new BlockTypeInfo(
                $oldBlock->getBreakInfo(),
                $oldBlock->getTypeTags(),
                $oldBlock->getEnchantmentTags()
            )
        );
        if (explode(":", $typename)[0] != "minecraft") {
            CustomiesBlockFactory::getInstance()->update($typename, $block);
        }

        (function(Block $block) : void {
            $typeId = $block->getTypeId();
            $this->typeIndex[$typeId] = clone $block;
            foreach ($block->generateStatePermutations() as $v) {
                $this->fillStaticArrays($v->getStateId(), $v);
            }
        })->call(RuntimeBlockStateRegistry::getInstance(), $block);

        $reflection = new \ReflectionClass(VanillaBlocks::class);
        /** @var array<string, Block> $blocks */
        $blocks = $reflection->getStaticPropertyValue("members");
        $blocks[mb_strtoupper($enumName)] = clone $block;
        $reflection->setStaticPropertyValue("members", $blocks);

        //        (function(string $id, \Closure $deserializer) : void {
        //            $this->deserializers[$id] = $deserializer;
        //        })->call(
        //            GlobalItemDataHandlers::getDeserializer(),
        //            $typeName,
        //            fn (SavedItemData $data) => $block->asItem()
        //        );
        (function(Block $block, \Closure $serializer) : void {
            $this->blockItemSerializers[$block->getTypeId()] = $serializer;
        })->call(
            GlobalItemDataHandlers::getSerializer(),
            $block,
            fn() => new SavedItemData($typename)
        );
    }

    private function finalizeOverride() : void {
        CreativeInventory::reset();
        CreativeInventory::getInstance();
        (function() : void {
            $this->craftingManager = CraftingManagerFromDataHelper::make(Path::join(\pocketmine\BEDROCK_DATA_PATH, "recipes"));
        })->call(Server::getInstance());
    }
}