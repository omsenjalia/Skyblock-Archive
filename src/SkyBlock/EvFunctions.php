<?php

namespace SkyBlock;

use alvin0319\CustomItemLoader\CustomItems;
use customiesdevs\customies\block\CustomiesBlockFactory;
use JsonException;
use pocketmine\block\{Block, BlockTypeIds, Crops as PMCrops, tile\Container, utils\FortuneDropHelper, VanillaBlocks};
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\entity\Skin;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\inventory\BaseInventory;
use pocketmine\item\{Axe,
    Durable,
    enchantment\EnchantmentInstance,
    Item,
    ItemBlock,
    ItemTypeIds,
    LegacyStringToItemParser,
    LegacyStringToItemParserException,
    Pickaxe,
    StringToItemParser,
    VanillaItems};
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use pocketmine\world\{particle\BlockBreakParticle, Position, sound\ChestOpenSound};
use SkyBlock\command\Functions;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\enchants\block\Barter;
use SkyBlock\enchants\block\BaseBlockBreakEnchant;
use SkyBlock\enchants\block\Insurance;
use SkyBlock\enchants\block\LuckOfTheSky;
use SkyBlock\enchants\block\Prosperity;
use SkyBlock\enchants\block\Tinkerer;
use SkyBlock\island\Island;
use SkyBlock\item\ItemManager;
use SkyBlock\perms\Permission;
use SkyBlock\tiles\AutoMinerTile;
use SkyBlock\tiles\AutoSellerTile;
use SkyBlock\tiles\MobSpawner;
use SkyBlock\tiles\OreGenTile;
use SkyBlock\user\User;
use SkyBlock\util\Util;
use SkyBlock\util\Values;

class EvFunctions {

    /** @var Main */
    private Main $pl;

    /**
     * EvFunctions constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->pl = $plugin;
    }

    /**
     * @param Island   $island
     * @param SBPlayer $player
     * @param int      $id
     * @param int      $meta
     * @param string   $type
     *
     * @return bool
     */
    public static function roleCheck(Island $island, Player $player, int $id, int $meta, string $type) : bool {
        if (($role = $island->getRole($player->getName())) !== null) {

            $farm = [
                BlockTypeIds::AIR,
                BlockTypeIds::MELON,
                BlockTypeIds::PUMPKIN,
                BlockTypeIds::CARROTS,
                BlockTypeIds::POTATOES,
                BlockTypeIds::BEETROOTS,
                BlockTypeIds::CACTUS,
                BlockTypeIds::SUGARCANE,
                BlockTypeIds::WHEAT,
                BlockTypeIds::NETHER_WART
            ];
            $crops = [
                BlockTypeIds::SUGARCANE,
                BlockTypeIds::BEETROOTS,
                BlockTypeIds::WHEAT,
                BlockTypeIds::CACTUS,
                BlockTypeIds::NETHER_WART,
                BlockTypeIds::POTATOES,
                BlockTypeIds::CARROTS,
                BlockTypeIds::PUMPKIN_STEM,
                BlockTypeIds::MELON_STEM,
                BlockTypeIds::FARMLAND,
                BlockTypeIds::GRASS,
                BlockTypeIds::DIRT,
                BlockTypeIds::AIR,
                ItemTypeIds::DYE
            ];
            $allblocks = $ores = [
                BlockTypeIds::AIR,
                BlockTypeIds::DIAMOND_ORE,
                BlockTypeIds::GOLD_ORE,
                BlockTypeIds::LAPIS_LAZULI_ORE,
                BlockTypeIds::IRON_ORE,
                BlockTypeIds::COAL_ORE,
                BlockTypeIds::COPPER_ORE,
                BlockTypeIds::EMERALD_ORE,
                BlockTypeIds::NETHER_QUARTZ_ORE,
                BlockTypeIds::ANCIENT_DEBRIS,
                BlockTypeIds::DEEPSLATE_COAL_ORE,
                BlockTypeIds::DEEPSLATE_COPPER_ORE,
                BlockTypeIds::DEEPSLATE_LAPIS_LAZULI_ORE,
                BlockTypeIds::DEEPSLATE_IRON_ORE,
                BlockTypeIds::DEEPSLATE_GOLD_ORE,
                BlockTypeIds::DEEPSLATE_DIAMOND_ORE,
                BlockTypeIds::DEEPSLATE_EMERALD_ORE,
                BlockTypeIds::QUARTZ,

                BlockTypeIds::NETHERITE
            ];
            array_push($allblocks, BlockTypeIds::NETHERRACK, BlockTypeIds::COBBLESTONE);

            if ($role == "miners") {
                if ($type == "break" or $type == "touch") {
                    if (in_array($id, $allblocks, true)) return true;
                }
            } elseif ($role == "builders") {
                if (!in_array($id, $ores, true) and !in_array($id, [BlockTypeIds::QUARTZ, BlockTypeIds::QUARTZ_STAIRS], true) and $id . ":" . $meta !== BlockTypeIds::STONE_SLAB . ":4") return true; // quartz + ores not allowed
            } elseif ($role == "placers") {
                if ($type == "touch") return true;
                if ($type == "place") {
                    if (!in_array($id, $ores, true)) return true;
                }
            } elseif ($role == "labourers") {
                if ($type == "touch") return true;
                if ($type == "place") {
                    if (in_array($id, $crops, true) or in_array($id, $allblocks, true)) return true;
                } elseif ($type == "break") {
                    if (in_array($id, $farm, true) or in_array($id, $allblocks, true)) return true;
                }
            } else {
                if ($type == "touch") return true;
                if ($type == "place") {
                    if (in_array($id, $crops, true)) return true;
                } elseif ($type == "break") {
                    if (in_array($id, $farm, true)) return true;
                }
            }
        }
        return false;
    }

    /**
     * @param Block $block
     *
     * @return bool
     */
    public static function isFarmRipe(Block $block) : bool {
        if (!$block instanceof PMCrops) return true;
        switch ($block->getTypeId()) {
            case BlockTypeIds::CARROTS:
            case BlockTypeIds::POTATOES:
            case BlockTypeIds::BEETROOTS:
            case BlockTypeIds::WHEAT:
                if ($block->getAge() < 7) {
                    return false;
                }
                break;
            case BlockTypeIds::NETHER_WART:
                if ($block->getAge() < 3) {
                    return false;
                }
                break;
            case BlockTypeIds::PUMPKIN_STEM:
            case BlockTypeIds::MELON_STEM:
            case BlockTypeIds::OAK_SAPLING:
            case BlockTypeIds::BIRCH_SAPLING:
                return false;
        }
        return true;
    }

    /**
     * @param Item  $item
     * @param Block $block
     *
     * @return array
     */
    public static function getFarming(Item $item, Block $block) : array {
        $farming = 0;
        $mana = 0;
        switch ($block->getTypeId()) {
            case BlockTypeIds::PUMPKIN:
                $farming = Data::$pumpkinFarming;
                $mana = Data::$pumpkinMana;
                break;
            case BlockTypeIds::MELON:
                $farming = Data::$melonFarming;
                $mana = Data::$melonMana;
                break;
            case BlockTypeIds::CARROTS:
                /** @var PMCrops $block */
                if ($block->getAge() >= 7) {
                    $farming = Data::$carrotFarming;
                    $mana = Data::$carrotMana;
                }
                break;
            case BlockTypeIds::SUGARCANE:
                $farming = Data::$sugarcaneFarming;
                $mana = Data::$sugarcaneMana;

                break;
            case BlockTypeIds::CACTUS:
                $farming = Data::$cactusMana;
                $mana = Data::$cactusMana;

                break;
            case BlockTypeIds::POTATOES:
                /** @var PMCrops $block */
                if ($block->getAge() >= 7) {
                    $farming = Data::$potatoFarming;
                    $mana = Data::$potatoMana;
                }
                break;
            case BlockTypeIds::BEETROOTS:
                /** @var PMCrops $block */
                if ($block->getAge() >= 7) {
                    $farming = Data::$beetrootFarming;
                    $mana = Data::$beetrootMana;
                }
                break;
            case BlockTypeIds::NETHER_WART:
                /** @var PMCrops $block */
                if ($block->getAge() >= 3) {
                    $farming = Data::$netherWartFarming;
                    $mana = Data::$netherWartMana;
                }
                break;
            case BlockTypeIds::WHEAT:
                /** @var PMCrops $block */
                if ($block->getAge() >= 7) {
                    $farming = Data::$wheatFarming;
                    $mana = Data::$wheatMana;
                }
                break;
        }
        return [$farming, $mana];
    }

    /**
     * @param User            $user
     * @param Block           $block
     * @param Island          $island
     * @param BlockBreakEvent $ev
     */
    public function checkBlockBreak(User $user, Block $block, Island $island, BlockBreakEvent $ev) : void {
        $data = self::getFarming($ev->getPlayer()->getInventory()->getItemInHand(), $block);
        [$farming, $mana] = $data;

        if ($farming > 0) {
            if (!$island->hasARole($user->getName()) && !$island->hasPerm($user->getName(), Permission::FARM)) {
                $this->sendMessage($user->getPlayer(), TextFormat::RED . "You dont have farming perms on this island");
                $ev->cancel();
                return;
            } else {
                $user->setPoints($farming, "farming");
                $island->setPoints($farming);
            }
        }
        if ($mana > 0) {
            $user->addMana($mana);
        }
    }

    /**
     * @param Event $ev
     * @param Block $block
     *
     * @return int
     */
    public function getPoints(Event $ev, Block $block) : int {

        if (!self::isFarmRipe($block)) return 0;
        return match ($block->getTypeId()) {
            BlockTypeIds::COAL_ORE => -1,
            BlockTypeIds::COPPER_ORE => -2,
            BlockTypeIds::IRON_ORE => -3,
            BlockTypeIds::LAPIS_LAZULI_ORE => -4,
            BlockTypeIds::GOLD_ORE => -5,
            BlockTypeIds::DIAMOND_ORE => -6,
            BlockTypeIds::EMERALD_ORE => -7,
            BlockTypeIds::NETHER_QUARTZ_ORE => -8,
            BlockTypeIds::ANCIENT_DEBRIS => -9,
            BlockTypeIds::DEEPSLATE_COAL_ORE => -10,
            BlockTypeIds::DEEPSLATE_COPPER_ORE => -11,
            BlockTypeIds::DEEPSLATE_IRON_ORE => -12,
            BlockTypeIds::DEEPSLATE_GOLD_ORE => -13,
            BlockTypeIds::DEEPSLATE_LAPIS_LAZULI_ORE => -14,
            BlockTypeIds::DEEPSLATE_DIAMOND_ORE => -15,
            BlockTypeIds::DEEPSLATE_EMERALD_ORE => -16,
            BlockTypeIds::QUARTZ => -17,
            BlockTypeIds::NETHERITE => -18,

            BlockTypeIds::COAL, BlockTypeIds::OBSIDIAN => 5,
            BlockTypeIds::IRON => 10,
            BlockTypeIds::GOLD => 12,
            BlockTypeIds::LAPIS_LAZULI => 15,
            BlockTypeIds::DIAMOND => 18,
            BlockTypeIds::EMERALD => 20,
            default => 1,
        };
    }

    /**
     * @param array    $words
     * @param Item     $item
     * @param SBPlayer $player
     *
     * @return bool
     */
    public function checkTagName(array $words, Item $item, Player $player) : bool {
        if ($words[2] == '§r§bTag') {
            $tagname = $words[1];
            $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
            $id = $this->pl->getTagManager()->getTagId($tagname);
            if ($user->hasTag($id)) {
                $this->sendMessage($player, "§cYou already have {$tagname} §ctag! §bUse /tag to equip");
                return false;
            }
            $cost = 5000;
            if (!$user->removeMoney($cost)) {
                $this->sendMessage($player, "§cYou need §6$cost$ §cto redeem a tag!");
                return false;
            }
            $user->addTag($id);
            $this->sendMessage($player, "{$tagname} §etag was redeemed successfully! §bUse /tag to equip!");
            $item->setCount($item->getCount() - 1);
            $inventory = $player->getInventory();
            $inventory->setItemInHand($item);
            return true;
        }
        return false;
    }

    /**
     * @param string $orename
     * @param string $orename2
     * @param int    $level
     * @param int    $maxlevel
     *
     * @return Item|null
     */
    public function getOreGenBlock(string $orename, string $orename2 = BlockTypeNames::AIR, int $level = 1, int $maxlevel = 8) : ?Item {
        if (!isset($this->pl->oregens[$orename])) return null;
        $block = CustomiesBlockFactory::getInstance()->get("fallentech:oregen");
        $item = $block->asItem();
        $item->setCustomName("§r§e§l{$this->pl->oregens[$orename]['name']} OreGen §6$level\n\n§r§fPlace this block anywhere\n§fon ground with upper\n§fblock as Air!");
        $tag = new CompoundTag();
        $tag->setString(OreGenTile::TAG_ORE_ID, $orename);
        $tag->setString(OreGenTile::TAG_ORE_ID2, $orename2);
        $tag->setInt(OreGenTile::TAG_LEVEL, $level);
        $tag->setInt(OreGenTile::MAX_LEVEL, $maxlevel);
        $item->setCustomBlockData($tag);
        return $item;
    }

    /**
     * @param string $id
     * @param int    $level
     * @param int    $count
     *
     * @return Item
     */
    public function getSpawnerBlock(string $id, int $level, int $count = 1) : Item {
        $item = VanillaBlocks::MONSTER_SPAWNER()->asItem()->setCount($count);
        foreach ($this->pl->spawners as $data) {
            if ($data['id'] === $id) {
                $item->setCustomName($data['name'] . " Spawner\n§eLevel §l§6" . $level . "§r");
                $tag = new CompoundTag();
                $tag->setString(MobSpawner::TAG_ENTITY_ID, $id);
                $tag->setInt(MobSpawner::TAG_LEVEL, $level);
                $item->setCustomBlockData($tag);
                break;
            }
        }
        return $item;
    }


    /**
     * @return Item
     */
    public function getCatalystBlock() : Item {
        $block = CustomiesBlockFactory::getInstance()->get("fallentech:catalyst");
        $item = $block->asItem();
        $item->setCustomName("§r§mCatalyst\n§r§fPlace this block anywhere\n§fon ground with upper\n§fblock as Air!");
        return $item;
    }

    /**
     * @param int $level
     * @param int $fortune
     * @param int $fortnite
     *
     * @return Item
     */
    public function getAutoMinerBlock(int $level, int $fortune = 0, int $fortnite = 1) : Item {
        $block = CustomiesBlockFactory::getInstance()->get("fallentech:autominer");
        $item = $block->asItem();
        if ($fortune >= 1) {
            $item->setCustomName("§r§e§lAutoMiner §6$level\n§r§awith Fortune §l§6$fortnite\n§r§fPlace this block on an ore\n§fgenerator with chest above\n§fthis block. The drop\n§fwill be added in the\n§fchest directly!");
        } else {
            $item->setCustomName("§r§e§lAutoMiner §6$level\n§r§fPlace this block on an ore\n§fgenerator with chest above\n§fthis block. The drop\n§fwill be added in the\n§fchest directly!");
        }
        $tag = new CompoundTag();
        $tag->setInt(AutoMinerTile::TAG_LEVEL, $level);
        $tag->setInt(AutoMinerTile::TAG_FORTUNE, $fortune);
        $tag->setInt(AutoMinerTile::TAG_FORTUNE_LEVEL, $fortnite);
        $item->setCustomBlockData($tag);
        return $item;
    }

    /**
     * @param int $aslevel
     * @param int $type
     *
     * @return Item
     */
    public function getAutoSellerBlock(int $aslevel, int $type = AutoSellerTile::TAG_TYPE_MONEY) : Item {
        $block = CustomiesBlockFactory::getInstance()->get("fallentech:autoseller");
        $item = $block->asItem();
        if ($type === AutoSellerTile::TAG_TYPE_XP) {
            $item->setCustomName("§r§e§lAutoSeller §6$aslevel\n§r§aType - §l§6XP\n§r§fPlace this block on a chest\n§fto auto sell contents.!");
        } else if ($type === AutoSellerTile::TAG_TYPE_MONEY) {
            $item->setCustomName("§r§e§lAutoSeller §6$aslevel\n§r§aType - §l§6Money\n§r§fPlace this block on a chest\n§fto auto sell contents.!");
        }
        $tag = new CompoundTag();
        $tag->setInt(AutoSellerTile::TAG_LEVEL, $aslevel);
        $tag->setInt(AutoSellerTile::TAG_TYPE, $type);
        $item->setCustomBlockData($tag);
        return $item;
    }

    /**
     * @param int $id
     *
     * @return array|null
     */
    public function getCropData(int $id) : ?array {
        return $this->pl->crops[$id] ?? null;
    }

    /**
     * @param SBPlayer $player
     * @param int      $blockid
     * @param int      $level
     *
     * @return bool
     */
    public function canPlaceSeed(Player $player, int $blockid, int $level) : bool {
        if (($cdata = $this->getCropData($blockid)) !== null) {
            if ($cdata['level'] > $level) {
                if (!isset($this->pl->using[strtolower($player->getName())]) || $this->pl->using[strtolower($player->getName())] <= time()) {
                    $this->pl->using[strtolower($player->getName())] = time() + 1;
                    $this->sendMessage($player, "§4[Error]§c §a{$cdata['name']} §ccrop will unlock at island level §b{$cdata['level']}§c. Do §e/crops §cfor more info!");
                }
                return false;
            }
        }
        return true;
    }

    /**
     * @param SBPlayer $sender
     * @param          $message
     */
    public function sendMessage(Player $sender, $message) : void {
        $sender->sendMessage(Values::FT_PREFIX . $message);
    }

    /**
     * @param SBPlayer $player
     * @param          $shop
     * @param          $loc
     */
    public function checkSignShop(Player $player, $shop, $loc) : void {
        $id = $shop["itemName"];
        try {
            $item = StringToItemParser::getInstance()->parse($id) ?? LegacyStringToItemParser::getInstance()->parse($id);
        } catch (LegacyStringToItemParserException) {
            return;
        }
        $name = $item->getName();
        $price = Util::convertToFloat($shop["price"]);
        $amount = $shop["amount"];
        if (strtolower($shop["owner"]) == strtolower($player->getName())) {
            $this->sendMessage($player, TF::RED . "> You can't buy from your own shop!");
            return;
        }
        if ($price < 1 or $amount < 1) {
            $this->sendMessage($player, TF::RED . "> Amount or item count not valid of the shop!");
            return;
        }
        if (!($cloud = $this->getCloudForPlayer($shop["owner"])) instanceof ItemCloud) {
            $this->sendMessage($player, TF::RED . "> Shop owner doesn't have an itemcloud account!");
            return;
        }
        if ($cloud->isLock()) {
            $this->sendMessage($player, TF::RED . "> Shop owner has their Itemcloud locked!");
            return;
        }

        $now = microtime(true);
        if (!isset($this->pl->tap[$player->getName()]) or $now - $this->pl->tap[$player->getName()][1] >= 1.5 or $this->pl->tap[$player->getName()][0] !== $loc) {
            $this->pl->tap[$player->getName()] = [$loc, $now];
            $this->sendMessage($player, TF::YELLOW . "Please tap again if you wanna buy " . TF::GREEN . number_format($amount) . TF::YELLOW . " of " . TF::AQUA . "$name " . TF::YELLOW . "for " . TF::GOLD . "$price" . "$");
            return;
        } else {
            unset($this->pl->tap[$player->getName()]);
        }

        if ($amount > $cloud->getCount(Item::nbtDeserialize($shop["item"])->getVanillaName())) {
            $this->sendMessage($player, TF::RED . "> This shop doesnt have enough item. §eThe owner §a{$shop["owner"]} §eof the shop didn't refill the stock on his itemcloud account! §c/ic help");
            return;
        }
        //        $item = ItemFactory::getInstance()->get($shop["item"], $shop["meta"], $amount);
        //        $item = LegacyStringToItemParser::getInstance()->parse($shop["item"].":".$shop["meta"])->setCount($amount);
        $item = Item::nbtDeserialize($shop["item"])->setCount($amount);
        if (!$player->getInventory()->canAddItem($item)) {
            $this->sendMessage($player, TF::RED . "> You don't have enough space in your inventory to purchase this item!");
            return;
        }
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        if (!$user->removeMoney($price)) {
            $this->sendMessage($player, TF::RED . "> You don't have §6" . number_format($price) . "§e$ §cto buy this item!");
            return;
        }
        $player->getInventory()->addItem($item);
        $this->sendMessage($player, TF::YELLOW . "You successfully bought §7x§c{$amount} §eof §a{$name} §efor §6" . number_format($price) . "§e$ from §a{$shop["owner"]}§e's PShop!");
        $cloud->removeItem(Item::nbtDeserialize($shop["item"])->getVanillaName(), 0);
        if (($user2 = $this->pl->getUserManager()->getOnlineUser($shop["owner"])) !== null) {
            $user2->addMoney($price, false);
            $this->sendMessage($user2->getPlayer(), "§a{$player->getName()} §ebought §7x§c{$amount} §eof §a{$name} §efor §6" . number_format($price) . "§e$ from your PShop!");
        } else    $this->pl->getDb()->addUserMoney($shop["owner"], $price);
    }

    /**
     * @param string $player
     *
     * @return null|ItemCloud
     */
    public function getCloudForPlayer(string $player) : ?ItemCloud {
        return $this->pl->clouds[strtolower($player)] ?? null;
    }

    /**
     * @param Position $sign
     *
     * @return bool
     */
    public function isPlayerShopSign(Position $sign) : bool {
        return isset($this->pl->shops[$sign->getX() . ":" . $sign->getY() . ":" . $sign->getZ() . ":" . $sign->getWorld()->getDisplayName()]);
    }

    /**
     * @param SBPlayer $player
     * @param Block    $block
     *
     * @return bool
     */
    public function checkPlayerShop(Player $player, Block $block) : bool {
        $loc = $block->getPosition()->getX() . ":" . $block->getPosition()->getY() . ":" . $block->getPosition()->getZ() . ":" . $block->getPosition()->getWorld()->getDisplayName();
        if (isset($this->pl->shops[$loc])) {
            $shop = $this->pl->shops[$loc];
            if ($shop["owner"] == $player->getName()) {
                unset($this->pl->shops[$loc]);
                $this->sendMessage($player, TF::YELLOW . "> Your shop was successfully removed!");
                return true;
            } else {
                if ($this->pl->hasOp($player)) {
                    $this->sendMessage($player, TF::YELLOW . "> You successfully removed §a{$shop["owner"]}§e's shop!");
                    unset($this->pl->shops[$loc]);
                    return true;
                } else {
                    $this->sendMessage($player, TF::RED . "> It's not your shop to remove!");
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param Block $block
     */
    public function forceDestroyShop(Block $block) : void {
        unset($this->pl->shops[$block->getPosition()->getX() . ":" . $block->getPosition()->getY() . ":" . $block->getPosition()->getZ() . ":" . $block->getPosition()->getWorld()->getDisplayName()]);
    }

    /**
     * @param int $level
     *
     * @return int
     */
    public function getLevel(int $level = 1) : int {
        return (int) ceil($level / 2);
    }

    /**
     * @param array    $words
     * @param Item     $item
     * @param Block    $block
     * @param SBPlayer $player
     */
    public function checkCustomName(array $words, Item $item, Block $block, Player $player) : void {
        $inventory = $player->getInventory();
        foreach ($words as $w) {
            switch ($w) {
                case "§6Vaulted":
                    if ($item->getTypeId() !== ItemTypeIds::BOOK) {
                        break;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($player)) {
                        $this->sendMessage($player, "§cYour Inventory is full. Empty a slot from your inventory!");
                        break;
                    }
                    $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                    if (!$user->removeMoney(500000)) {
                        $this->sendMessage($player, "§cYou need 500,000$ to redeem a Vaulted Custom Enchant Book!");
                        break;
                    }
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $item = CustomItems::VAULTED_BOOK();
                    $ench = $this->randomEnchantment('vaulted');
                    $name = $ench[0];
                    $type = $ench[4];
                    $chance = mt_rand(1, 99);
                    $inventory->setItem($inventory->firstEmpty(), $item->setCustomName(TF::RESET . " §l{$this->getColorForEnchant($ench[3])}$name \n §r{$this->getColorForType($type)}$type Custom Enchant \n §a$chance%% §fAccuracy \n §bUse /combiner to merge this enchant!"));
                    $this->sendMessage($player, "§eSuccessfully redeemed for 500,000$, Added $name §aVaulted Custom Enchanted Book!\n§bUse /combiner to merge it with a $type! To increase accuracy of the book, use an Enchanter Scroll by /enchanter");
                    break;
                case "§6Common":
                    if ($item->getTypeId() != ItemTypeIds::BOOK) {
                        break;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($player)) {
                        $this->sendMessage($player, "§cYour Inventory is full. Empty a slot from your inventory!");
                        break;
                    }
                    $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                    if (!$user->removeMoney(5000)) {
                        $this->sendMessage($player, "§cYou need 5,000$ to redeem a Common Custom Enchant Book!");
                        break;
                    }
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $item = CustomItems::COMMON_BOOK();
                    $ench = $this->randomEnchantment("common");
                    $name = $ench[0];
                    $type = $ench[4];
                    $chance = mt_rand(1, 99);
                    $inventory->setItem($inventory->firstEmpty(), $item->setCustomName(TF::RESET . " §l{$this->getColorForEnchant($ench[3])}$name \n §r{$this->getColorForType($type)}$type Custom Enchant \n §a$chance%% §fAccuracy \n §bUse /combiner to merge this enchant!"));
                    $this->sendMessage($player, "§eSuccessfully redeemed for 5,000$, Added $name §aCommon Custom Enchanted Book!\n§bUse /combiner to merge it with a $type! To increase accuracy of the book, use an Enchanter Scroll by /enchanter");
                    break;
                case "§6Rare":
                    if ($item->getTypeId() != ItemTypeIds::BOOK) {
                        break;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($player)) {
                        $this->sendMessage($player, "§cYour Inventory is full. Empty a slot from your inventory!");
                        break;
                    }
                    $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                    if (!$user->removeMoney(25000)) {
                        $this->sendMessage($player, "§cYou need 25,000$ to redeem a Rare Custom Enchant Book!");
                        break;
                    }
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $item = CustomItems::RARE_BOOK();
                    $ench = $this->randomEnchantment("rare");
                    $name = $ench[0];
                    $type = $ench[4];
                    $chance = mt_rand(1, 99);
                    $inventory->setItem($inventory->firstEmpty(), $item->setCustomName(TF::RESET . " §l{$this->getColorForEnchant($ench[3])}$name \n §r{$this->getColorForType($type)}$type Custom Enchant \n §a$chance%% §fAccuracy \n §bUse /combiner to merge this enchant!"));
                    $this->sendMessage($player, "§eSuccessfully redeemed for 25,000$, Added $name §aRare Custom Enchanted Book!\n§bUse /combiner to merge it with a $type! To increase accuracy of the book, use an Enchanter Scroll by /enchanter");
                    break;
                case "§6Legendary":
                    if ($item->getTypeId() != ItemTypeIds::BOOK) {
                        break;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($player)) {
                        $this->sendMessage($player, "§cYour Inventory is full. Empty a slot from your inventory!");
                        break;
                    }
                    $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                    if (!$user->removeMoney(75000)) {
                        $this->sendMessage($player, "§cYou need 75,000$ to redeem a Legendary Custom Enchant Book!");
                        break;
                    }
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $item = CustomItems::LEGENDARY_BOOK();
                    $ench = $this->randomEnchantment("legendary");
                    $name = $ench[0];
                    $type = $ench[4];
                    $chance = mt_rand(1, 99);
                    $inventory->setItem($inventory->firstEmpty(), $item->setCustomName(TF::RESET . " §l{$this->getColorForEnchant($ench[3])}$name \n §r{$this->getColorForType($type)}$type Custom Enchant \n §a$chance%% §fAccuracy \n §bUse /combiner to merge this enchant!"));
                    $this->sendMessage($player, "§eSuccessfully redeemed for 75,000$, Added $name §aLegendary Custom Enchanted Book!\n§bUse /combiner to merge it with a $type! To increase accuracy of the book, use an Enchanter Scroll by /enchanter");
                    break;
                case "§6Exclusive":
                    if ($item->getTypeId() != ItemTypeIds::BOOK) {
                        break;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($player)) {
                        $this->sendMessage($player, "§cYour Inventory is full. Empty a slot from your inventory!");
                        break;
                    }
                    $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                    if (!$user->removeMoney(150000)) {
                        $this->sendMessage($player, "§cYou need 150,000$ to redeem a Exclusive Custom Enchant Book!");
                        break;
                    }
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $item = CustomItems::EXCLUSIVE_BOOK();
                    $ench = $this->randomEnchantment("exclusive");
                    $name = $ench[0];
                    $type = $ench[4];
                    $chance = mt_rand(1, 99);
                    $inventory->setItem($inventory->firstEmpty(), $item->setCustomName(TF::RESET . " §l{$this->getColorForEnchant($ench[3])}$name \n §r{$this->getColorForType($type)}$type Custom Enchant \n §a$chance%% §fAccuracy \n §bUse /combiner to merge this enchant!"));
                    $this->sendMessage($player, "§eSuccessfully redeemed for 150,000$, Added $name §bExclusive Custom Enchanted Book!\n§eUse /combiner to merge it with a $type! To increase accuracy of the book, use an Enchanter Scroll by /enchanter");
                    break;
                case "§6Ancient":
                    if ($item->getTypeId() != ItemTypeIds::BOOK) {
                        break;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($player)) {
                        $this->sendMessage($player, "§cYour Inventory is full. Empty a slot from your inventory!");
                        break;
                    }
                    $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                    if (!$user->removeMoney(5000000)) {
                        $this->sendMessage($player, "§cYou need 5,000,000$ to redeem an Ancient Custom Enchant Book!");
                        break;
                    }
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $item = CustomItems::ANCIENT_BOOK();
                    $ench = $this->randomEnchantment("ancient");
                    $name = $ench[0];
                    $type = $ench[4];
                    $chance = mt_rand(1, 99);
                    $inventory->setItem($inventory->firstEmpty(), $item->setCustomName(TF::RESET . " §l{$this->getColorForEnchant($ench[3])}$name \n §r{$this->getColorForType($type)}$type Custom Enchant \n §a$chance%% §fAccuracy \n §bUse /combiner to merge this enchant!"));
                    $this->sendMessage($player, "§eSuccessfully redeemed for 5,000,000$, Added $name §1Ancient Custom Enchanted Book!\n§eUse /combiner to merge it with a $type! To increase accuracy of the book, use an Enchanter Scroll by /enchanter");
                    break;
                case "§r§6Vote":
                    if (!($item->getTypeId() == ItemTypeIds::SLIMEBALL || $item->getTypeId() == CustomItems::VOTE_KEY()->getTypeId())) {
                        break;
                    }
                    if ($block->getTypeId() != BlockTypeIds::CHEST) {
                        break;
                    }
                    if ($player->getWorld()->getDisplayName() != "lobby") {
                        break;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($player)) {
                        $this->sendMessage($player, "§cYour Inventory is full. Empty a slot from your inventory!");
                        break;
                    }
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $this->checkVoteKey($player);
                    self::addCrateParticle($block, 'vote');
                    break;
                case "§r§aCommon":
                    if (!($item->getTypeId() == ItemTypeIds::MAGMA_CREAM || $item->getTypeId() == CustomItems::COMMON_KEY()->getTypeId())) {
                        break;
                    }
                    if ($block->getTypeId() != BlockTypeIds::CHEST) {
                        break;
                    }
                    if ($player->getWorld()->getDisplayName() != "lobby") {
                        break;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($player)) {
                        $this->sendMessage($player, "§cYour Inventory is full. Empty a slot from your inventory!");
                        break;
                    }
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $this->checkCommonKey($player);
                    self::addCrateParticle($block, 'common');
                    break;
                case "§r§bRare":
                    if (!($item->getTypeId() == ItemTypeIds::GHAST_TEAR || $item->getTypeId() == CustomItems::RARE_KEY()->getTypeId())) {
                        break;
                    }
                    if ($player->getWorld()->getDisplayName() != "lobby") {
                        break;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($player)) {
                        $this->sendMessage($player, "§cYour Inventory is full. Empty a slot from your inventory!");
                        break;
                    }
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $this->checkRareKey($player);
                    self::addCrateParticle($block, 'rare');
                    break;
                case "§r§e§lLegendary":
                    if (!($item->getTypeId() == ItemTypeIds::RAW_FISH || $item->getTypeId() == CustomItems::LEGENDARY_KEY()->getTypeId())) {
                        break;
                    }
                    if ($block->getTypeId() != BlockTypeIds::CHEST) {
                        break;
                    }
                    if ($player->getWorld()->getDisplayName() != "lobby") {
                        break;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($player)) {
                        $this->sendMessage($player, "§cYour Inventory is full. Empty a slot from your inventory!");
                        break;
                    }
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $this->checkLegendaryKey($player);
                    self::addCrateParticle($block, 'legendary');
                    break;
                case "§r§d§lMystic":
                    if (!($item->getTypeId() == ItemTypeIds::DRIED_KELP /*TODO <--- not dried kelp?*/ || $item->getTypeId() == CustomItems::MYSTIC_KEY()->getTypeId())) {
                        break;
                    }
                    if ($block->getTypeId() != BlockTypeIds::CHEST) {
                        break;
                    }
                    if ($player->getWorld()->getDisplayName() != "lobby") {
                        break;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($player)) {
                        $this->sendMessage($player, "§cYour Inventory is full. Empty a slot from your inventory!");
                        break;
                    }
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $this->checkMysticKey($player);
                    self::addCrateParticle($block, 'mystic');
                    break;
                case "§r§l§7§k:§r§l§9CE§7§k:§r":
                    if (!$item->getTypeId() == CustomItems::CE_KEY()->getTypeId()) {
                        break;
                    }
                    if ($block->getTypeId() != BlockTypeIds::CHEST) {
                        break;
                    }
                    if ($player->getWorld()->getDisplayName() != "lobby") {
                        break;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($player)) {
                        $this->sendMessage($player, "§cYour Inventory is full. Empty a slot from your inventory!");
                        break;
                    }
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $this->checkCeKey($player);
                    self::addCrateParticle($block, 'mystic');
                    break;
                case "§r§l§7§k:§r§l§cVE§7§k:§r":
                    if (!$item->getTypeId() == CustomItems::VE_KEY()->getTypeId()) {
                        break;
                    }
                    if ($block->getTypeId() != BlockTypeIds::CHEST) {
                        break;
                    }
                    if ($player->getWorld()->getDisplayName() != "lobby") {
                        break;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($player)) {
                        $this->sendMessage($player, "§cYour Inventory is full. Empty a slot from your inventory!");
                        break;
                    }
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $this->checkVeKey($player);
                    self::addCrateParticle($block, 'mystic');
                    break;
            }
        }
    }

    /**
     * @param Block  $block
     * @param string $type
     */
    public static function addCrateParticle(Block $block, string $type = 'common') : void {
        $newPos = $block->getPosition()->add(0.5, 0.5, 0.5);
        if ($type == 'common') $block->getPosition()->getWorld()->addParticle($newPos, new BlockBreakParticle(VanillaBlocks::LAPIS_LAZULI()));
        if ($type == 'rare') $block->getPosition()->getWorld()->addParticle($newPos, new BlockBreakParticle(VanillaBlocks::GOLD()));
        if ($type == 'vote') $block->getPosition()->getWorld()->addParticle($newPos, new BlockBreakParticle(VanillaBlocks::CHEST()));
        if ($type == 'legendary') $block->getPosition()->getWorld()->addParticle($newPos, new BlockBreakParticle(VanillaBlocks::DIAMOND()));
        if ($type == 'mystic') $block->getPosition()->getWorld()->addParticle($newPos, new BlockBreakParticle(VanillaBlocks::PURPLE_TORCH()));
        if ($type == 'ce') $block->getPosition()->getWorld()->addParticle($newPos, new BlockBreakParticle(VanillaBlocks::TORCH()));
        if ($type == 've') $block->getPosition()->getWorld()->addParticle($newPos, new BlockBreakParticle(VanillaBlocks::TORCH()));
        $block->getPosition()->getWorld()->addSound($block->getPosition(), new ChestOpenSound());
    }

    /**
     * @param string $type
     *
     * @return array|null
     */
    public function randomEnchantment(string $type = "common") : ?array {
        $type = ucfirst(strtolower($type));
        if ($type == 'Vaulted') {
            $vaulted = $this->pl->getVaulted();
            $id = $vaulted[mt_rand(0, count($vaulted) - 1)];
        } else {
            $id = 0;
            foreach ($this->pl->getFunctions()->getRandomEnchantments() as $key => $ench) {
                if ($ench[3] === $type and !$this->pl->isVaulted($key)) {
                    $id = $key;
                    break;
                }
            }
        }
        return $this->pl->getEnchantments()[$id] ?? null;
    }

    /**
     * @param string $rarity
     *
     * @return string
     */
    public function getColorForEnchant(string $rarity) : string {
        return match (strtolower($rarity)) {
            "common" => '§7',
            "rare" => '§6',
            "legendary" => '§a',
            "exclusive" => '§b',
            "ancient" => '§1',
            default => '',
        };
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getColorForType(string $type) : string {
        return match (strtolower($type)) {
            "sword" => '§a',
            "axe" => '§f',
            "pickaxe" => '§d',
            "bow" => '§c',
            "armor" => '§e',
            "helmet", "chestplate", "leggings", "boots" => '§6',
            default => '',
        };
    }

    /**
     * @param SBPlayer $p
     */
    public function checkVoteKey(Player $p) : void {
        switch (mt_rand(1, 23)) {
            case 1:
                $this->sendMessage($p, "§6Used Vote Key! §fCheck your Inventory, you got§e§l Apples!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(260, 0, mt_rand(2, 10));
                $item = VanillaItems::APPLE()->setCount(mt_rand(2, 10));
                $p->getInventory()->addItem($item);
                break;
            case 2:
                $this->sendMessage($p, "§6Used Vote Key! §fCheck your Inventory, you got a§e§l Crafting Table!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(58, 0, 1);
                $item = VanillaBlocks::CRAFTING_TABLE()->asItem();
                $p->getInventory()->addItem($item);
                break;
            case 3:
                $this->sendMessage($p, "§6Used Vote Key! §fCheck your Inventory, you got§e§l Brewing Table!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(379, 0, 1);
                $item = VanillaBlocks::BREWING_STAND()->asItem();
                $p->getInventory()->addItem($item);
                break;
            case 4:
                $this->sendMessage($p, "§6Used Vote Key! §fCheck your Inventory, you got an§e§l Iron Block!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(42, 0, 1);
                $item = VanillaBlocks::IRON()->asItem();
                $p->getInventory()->addItem($item);
                break;
            case 5:
                $this->sendMessage($p, "§6Used Vote Key! §fCheck your Inventory, you got§e§l Diamond Sword!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(276, 0, 1);
                $item = VanillaItems::DIAMOND_SWORD();
                $p->getInventory()->addItem($item);
                break;
            case 6:
                $this->sendMessage($p, "§6Used Vote Key! §fCheck your Inventory, you got§e§l Iron Pickaxe!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(257, 0, 1);
                $item = VanillaItems::IRON_PICKAXE();
                $p->getInventory()->addItem($item);
                break;
            case 7:
                $this->sendMessage($p, "§6Used Vote Key! §fCheck your Inventory, you got §e§lWood!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(17, 0, mt_rand(30, 64));
                $item = VanillaBlocks::OAK_WOOD()->asItem()->setCount(mt_rand(30, 64));
                $p->getInventory()->addItem($item);
                break;
            case 8:
                $this->sendMessage($p, "§6Used Vote Key! §fCheck your Inventory, you got a§e§l Rare Crate Key! §aUse it on Rare Crate Chest!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getCrateKeys('rare'));
                break;
            case 9:
                $this->sendMessage($p, "§6Used Vote Key! §fCheck your Inventory, you got a§e§l Common Crate Key {Magma Cream}! §aUse it on Common Crate Chest!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getCrateKeys('common'));
                break;
            case 10:
                $this->sendMessage($p, "§6Used Vote Key! You win a Jackpot! §fCheck your Inventory, you got §e§lDiamonds!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(264, 0, mt_rand(1, 5));
                $item = VanillaItems::DIAMOND()->setCount(mt_rand(1, 5));
                $p->getInventory()->addItem($item);
                break;
            case 11:
                $this->sendMessage($p, "§6Used Vote Key! You win a Jackpot! §eYou get 5,000$!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $user = $this->pl->getUserManager()->getOnlineUser($p->getName());
                $user->addMoney(50000);
                break;
            case 12:
                $this->sendMessage($p, "§6Used Vote Key! §fCheck your Inventory, you got a§e§l Gold Block!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(41, 0, 1);
                $item = VanillaBlocks::GOLD()->asItem();
                $p->getInventory()->addItem($item);
                break;
            case 13:
                $this->sendMessage($p, "§6Used Vote Key! §fCheck your Inventory, you got a§e§l Diamond Block!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(57, 0, 1);
                $item = VanillaBlocks::DIAMOND()->asItem();
                $p->getInventory()->addItem($item);
                break;
            case 14:
                $this->sendMessage($p, "§6Used Vote Key! §fCheck your Inventory, you got a§e§l Redstone Block!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(152, 0, 1);
                $item = VanillaBlocks::REDSTONE()->asItem();
                $p->getInventory()->addItem($item);
                break;
            case 15:
                $this->sendMessage($p, "§6Used Vote Key! §fCheck your Inventory, you got a§e§l Lapis Lazuli Block!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(22, 0, 1);
                $item = VanillaBlocks::LAPIS_LAZULI()->asItem();
                $p->getInventory()->addItem($item);
                break;
            case 16:
                $this->sendMessage($p, "§6Used Vote Key! §fCheck your Inventory, you got a§e§l Tag!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getTagManager()->getRandomTag());
                break;
            case 17:
                $this->sendMessage($p, "§6Used Vote Key! §fYou got a level 1 OP Sword!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getFunctions()->opSword(1));
                break;
            default:
                $this->sendMessage($p, "§6Used Vote Key! §fYou got §e§lNothing, try again!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                break;
        }
    }

    /**
     * @param SBPlayer $p
     */
    public function checkCommonKey(Player $p) : void {
        switch (mt_rand(1, 25)) {
            case 1:
                $this->sendMessage($p, "§aUsed Common Key! §fCheck your Inventory, you got§e§l Apples!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(260, 0, mt_rand(2, 15));
                $item = VanillaItems::APPLE()->setCount(mt_rand(2, 15));
                $p->getInventory()->addItem($item);
                break;
            case 2:
                $this->sendMessage($p, "§aUsed Common Key! §fCheck your Inventory, you got a§e§l Iron Blocks!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(42, 0, 2);
                $item = VanillaBlocks::IRON()->asItem()->setCount(2);
                $p->getInventory()->addItem($item);
                break;
            case 3:
                $this->sendMessage($p, "§aUsed Common Key! §fCheck your Inventory, you got§e§l Diamond Swords!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(276, 0, 2);
                $item = VanillaItems::DIAMOND_SWORD()->setCount(2);
                $p->getInventory()->addItem($item);
                break;
            case 4:
                $this->sendMessage($p, "§aUsed Common Key! §fCheck your Inventory, you got§e§l Diamond Pickaxe!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(278, 0, 1);
                $item = VanillaItems::DIAMOND_PICKAXE();
                $p->getInventory()->addItem($item);
                break;
            case 5:
                $this->sendMessage($p, "§aUsed Common Key! §fCheck your Inventory, you got§e§l Vote Crate Keys! §aUse it on Vote Crate Chest!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getCrateKeys('vote', 3));
                break;
            case 6:
                $this->sendMessage($p, "§aUsed Common Key! §fCheck your Inventory, you got a§e§l Rare Crate Key! §aUse it on Rare Crate Chest!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getCrateKeys('rare'));
                break;
            case 7:
                $this->sendMessage($p, "§aUsed Common Key! You win a Jackpot! §fCheck your Inventory, you got §e§lDiamonds!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(264, 0, mt_rand(4, 10));
                $item = VanillaItems::DIAMOND()->setCount(mt_rand(4, 10));
                $p->getInventory()->addItem($item);
                break;
            case 8:
                $this->sendMessage($p, "§aUsed Common Key! You win a Jackpot! §eYou get 10,000$!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $user = $this->pl->getUserManager()->getOnlineUser($p->getName());
                $user->addMoney(10000);
                break;
            case 9:
                $this->sendMessage($p, "§aUsed Common Key! §fCheck your Inventory, you got§e§l Gold Blocks!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(41, 0, 2);
                $item = VanillaBlocks::GOLD()->asItem()->setCount(2);
                $p->getInventory()->addItem($item);
                break;
            case 10:
                $this->sendMessage($p, "§aUsed Common Key! §fCheck your Inventory, you got a§e§l Diamond Block!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(57, 0, 1);
                $item = VanillaBlocks::DIAMOND()->asItem();
                $p->getInventory()->addItem($item);
                break;
            case 11:
                $this->sendMessage($p, "§aUsed Common Key! §fCheck your Inventory, you got§e§l Redstone Blocks!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(152, 0, 2);
                $item = VanillaBlocks::REDSTONE()->asItem()->setCount(2);
                $p->getInventory()->addItem($item);
                break;
            case 12:
                $this->sendMessage($p, "§aUsed Common Key! §fCheck your Inventory, you got§l§e Lapis Lazuli Blocks!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(22, 0, 2);
                $item = VanillaBlocks::LAPIS_LAZULI()->asItem()->setCount(2);
                $p->getInventory()->addItem($item);
                break;
            case 13:
                $this->sendMessage($p, "§aUsed Common Key! §fYou got §e§lCobblestone!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(4, 0, mt_rand(15, 40));
                $item = VanillaBlocks::COBBLESTONE()->asItem()->setCount(mt_rand(15, 40));
                $p->getInventory()->addItem($item);
                break;
            case 14:
                $this->sendMessage($p, "§aUsed Common Key! §fYou got §e§lSand!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(12, 0, mt_rand(15, 40));
                $item = VanillaBlocks::SAND()->asItem()->setCount(mt_rand(15, 40));
                $p->getInventory()->addItem($item);
                break;
            case 15:
                $this->sendMessage($p, "§aUsed Common Key! §fYou got a §e§lCommon Book!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getCEBook();
                $p->getInventory()->addItem($item);
                break;
            case 18:
                $this->sendMessage($p, "§aUsed Common Key! §fYou got 200 casino chips, use them at /casino! Check by /mychips\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $user = $this->pl->getUserManager()->getOnlineUser($p->getName());
                $user->addChips(200);
                break;
            case 19:
                $this->sendMessage($p, "§aUsed Common Key! §fYou got an OP Sword!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getFunctions()->opSword(mt_rand(2, 4)));
                break;
            case 20:
            case 21:
                $this->sendMessage($p, "§aUsed Common Key! §fYou got a Tag!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getTagManager()->getRandomTag());
                break;
            default:
                $this->sendMessage($p, "§aUsed Common Key! §fYou got §e§lNothing, try again!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                break;
        }
    }

    /**
     * @param SBPlayer $p
     */
    public function checkRareKey(Player $p) : void {
        switch (mt_rand(1, 29)) {
            case 1:
                $this->sendMessage($p, "§bUsed Rare Key! §fCheck your Inventory, you got§e§l Golden Apples!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(322, 0, mt_rand(2, 5));
                $item = VanillaItems::GOLDEN_APPLE()->setCount(mt_rand(2, 5));
                $p->getInventory()->addItem($item);
                break;
            case 2:
                $this->sendMessage($p, "§bUsed Rare Key! §fCheck your Inventory, you got a§l§e Iron Blocks!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(42, 0, 3);
                $item = VanillaBlocks::IRON()->asItem()->setCount(3);
                $p->getInventory()->addItem($item);
                break;
            case 3:
                $this->sendMessage($p, "§bUsed Rare Key! §fCheck your Inventory, you got§e§l Diamond Swords!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(276, 0, 3);
                $item = VanillaItems::DIAMOND_SWORD()->setCount(3);
                $p->getInventory()->addItem($item);
                break;
            case 4:
                $this->sendMessage($p, "§bUsed Rare Key! §fCheck your Inventory, you got§e§l Diamond Pickaxes!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(278, 0, 2);
                $item = VanillaItems::DIAMOND_PICKAXE()->setCount(2);
                $p->getInventory()->addItem($item);
                break;
            case 5:
                $this->sendMessage($p, "§bUsed Rare Key! §fCheck your Inventory, you got§e§l VoteKey Crate Keys! §aUse it on Vote Crate Chest!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getCrateKeys('vote', 3));
                break;
            case 6:
                $this->sendMessage($p, "§bUsed Rare Key! You win a Jackpot! §fCheck your Inventory, you got §l§eDiamonds!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(264, 0, mt_rand(5, 15));
                $item = VanillaItems::DIAMOND()->setCount(mt_rand(5, 15));
                $p->getInventory()->addItem($item);
                break;
            case 7:
                $this->sendMessage($p, "§bUsed Rare Key! You win a Jackpot! §eYou get 25,000$!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $user = $this->pl->getUserManager()->getOnlineUser($p->getName());
                $user->addMoney(25000);
                break;
            case 8:
                $this->sendMessage($p, "§bUsed Rare Key! §fCheck your Inventory, you got§l§e Gold Blocks!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(41, 0, 3);
                $item = VanillaBlocks::GOLD()->asItem()->setCount(3);
                $p->getInventory()->addItem($item);
                break;
            case 9:
                $this->sendMessage($p, "§bUsed Rare Key! §fCheck your Inventory, you got§l§e Diamond Blocks!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(57, 0, 2);
                $item = VanillaBlocks::DIAMOND()->asItem()->setCount(2);
                $p->getInventory()->addItem($item);
                break;
            case 10:
                $this->sendMessage($p, "§bUsed Rare Key! §fCheck your Inventory, you got§l§e Redstone Blocks!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(152, 0, 3);
                $item = VanillaBlocks::REDSTONE()->asItem()->setCount(3);
                $p->getInventory()->addItem($item);
                break;
            case 11:
                $this->sendMessage($p, "§bUsed Rare Key! §fCheck your Inventory, you got§l§e Lapis Lazuli Blocks!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(22, 0, 3);
                $item = VanillaBlocks::LAPIS_LAZULI()->asItem()->setCount(3);
                $p->getInventory()->addItem($item);
                break;
            case 12:
                $this->sendMessage($p, "§bUsed Rare Key! §fYou got §e§lCobblestone!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(4, 0, mt_rand(20, 60));
                $item = VanillaBlocks::COBBLESTONE()->asItem()->setCount(mt_rand(20, 60));
                $p->getInventory()->addItem($item);
                break;
            case 13:
                $this->sendMessage($p, "§bUsed Rare Key! §fYou got §e§lSand!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(12, 0, mt_rand(20, 60));
                $item = VanillaBlocks::SAND()->asItem()->setCount(mt_rand(20, 60));
                $p->getInventory()->addItem($item);
                break;
            case 14:
                $this->sendMessage($p, "§bUsed Rare Key! §fYou got a §e§lRare Book!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getCEBook('rare');
                $p->getInventory()->addItem($item);
                break;
            case 15:
                $this->sendMessage($p, "§bUsed Rare Key! §fYou got a LevelUp Scroll!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getScrolls();
                $p->getInventory()->addItem($item);
                break;
            case 16:
                $this->sendMessage($p, "§bUsed Rare Key! §fYou got an Enchanter Scroll!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getScrolls('enchanter');
                $p->getInventory()->addItem($item);
                break;
            case 17:
                $this->sendMessage($p, "§bUsed Rare Key! §fYou got 350 Casino chips! Check by /mychips\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $user = $this->pl->getUserManager()->getOnlineUser($p->getName());
                $user->addChips(350);
                break;
            case 18:
                $this->sendMessage($p, "§bUsed Rare Key! §fYou got an OP Sword!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getFunctions()->opSword(mt_rand(3, 5)));
                break;
            case 19:
            case 20:
                $this->sendMessage($p, "§bUsed Rare Key! §fCheck your Inventory, you got§l§e Grass!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //            $item = ItemFactory::getInstance()->get(2, 0, mt_rand(10, 20));
                $item = VanillaBlocks::GRASS()->asItem()->setCount(mt_rand(10, 20));
                $p->getInventory()->addItem($item);
                break;
            case 21:
                $this->sendMessage($p, "§bUsed Rare Key! §fYou got a §bLegendary §fKey!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getCrateKeys('legendary');
                $p->getInventory()->addItem($item);
                break;
            case 22:
                $this->sendMessage($p, "§bUsed Rare Key! §fYou got a GOD Scroll!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getScrolls('god');
                $p->getInventory()->addItem($item);
                break;
            case 23:
            case 24:
                $this->sendMessage($p, "§bUsed Rare Key! §fCheck your inventory, you got a Tag!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getTagManager()->getRandomTag());
                break;
            case 25:
            case 26:
                $this->sendMessage($p, "§bUsed Rare Key! §fYou got a Inferno Scroll!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getScrolls('inferno');
                $p->getInventory()->addItem($item);
                break;
            default:
                $this->sendMessage($p, "§bUsed Rare Key! §fYou got §e§lNothing, try again!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                break;
        }
    }

    /**
     * @param SBPlayer $p
     */
    public function checkLegendaryKey(Player $p) : void {
        switch (mt_rand(1, 19)) {
            case 1:
                $this->sendMessage($p, "§eUsed Legendary Key! §fCheck your Inventory, you got§e§l Golden Enchanted Apples!\n§b§lBuy Legendary Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(466, 0, mt_rand(1, 3));
                $item = VanillaItems::GOLDEN_APPLE()->setCount(mt_rand(1, 3));
                $p->getInventory()->addItem($item);
                break;
            case 2:
                //				$this->sendMessage($p, "§eUsed Legendary Key! §fCheck your Inventory, you got§e§l Bedrock!\n§b§lBuy Legendary Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                ////                $item = ItemFactory::getInstance()->get(7, 0, mt_rand(30, 50));
                //				$item = VanillaBlocks::BEDROCK()->asItem()->setCount(mt_rand(30, 50));
                //				$p->getInventory()->addItem($item);
                break;
            case 3:
                $this->sendMessage($p, "§eUsed Legendary Key! §fCheck your Inventory, you got §l§eDiamonds!\n§b§lBuy Legendary Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(264, 0, mt_rand(35, 45));
                $item = VanillaItems::DIAMOND()->setCount(mt_rand(35, 45));
                $p->getInventory()->addItem($item);
                break;
            case 4:
                $this->sendMessage($p, "§eUsed Legendary Key! §fCheck your Inventory, you got §l§eLapis Lazuli!\n§b§lBuy Rare Legendary keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(351, 4, mt_rand(30, 35));
                $item = VanillaItems::LAPIS_LAZULI()->setCount(mt_rand(30, 35));
                $p->getInventory()->addItem($item);
                break;
            case 5:
                $this->sendMessage($p, "§eUsed Legendary Key! §fCheck your Inventory, you got a §l§eLegendary Book!\n§b§lBuy Legendary keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getCEBook('legendary');
                $p->getInventory()->addItem($item);
                break;
            case 6:
                $this->sendMessage($p, "§eUsed Legendary Key! §fYou got a LevelUp Scroll!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getScrolls('levelup');
                $p->getInventory()->addItem($item);
                break;
            case 7:
                $this->sendMessage($p, "§eUsed Legendary Key! §fYou got a Enchanter Scroll!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getScrolls('enchanter');
                $p->getInventory()->addItem($item);
                break;
            case 8:
                $this->sendMessage($p, "§eUsed Legendary Key! §fYou got 500 Casino chips! Check by /mychips\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $user = $this->pl->getUserManager()->getOnlineUser($p->getName());
                $user->addChips(500);
                break;
            case 9:
                $this->sendMessage($p, "§eUsed Legendary Key! §fYou got an OP Sword!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getFunctions()->opSword(mt_rand(3, 6)));
                break;
            case 10:
                $this->sendMessage($p, "§eUsed Legendary Key! §fCheck your inventory, you got a GOD Scroll!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getScrolls('god');
                $p->getInventory()->addItem($item);
                break;
            case 11:
            case 12:
                $this->sendMessage($p, "§eUsed Legendary Key! §fCheck your inventory, you got a Tag!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getTagManager()->getRandomTag());
                break;
            case 13:
                $this->sendMessage($p, "§eUsed Legendary Key! §fCheck your inventory, you got a Inferno Scroll!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getScrolls('inferno');
                $p->getInventory()->addItem($item);
                break;
            //            case 14:
            //				$i = mt_rand(1, 4);
            //				switch ($i) {
            //					case 0:
            //						$this->sendMessage($p, "§aUsed Legendary Key! §fYou got a §e§lBlood Dragon Mask!\n§b§lBuy Crate Keys at shop.fallentech.io for §aEPIC REWARDS!");
            ////						$p->getInventory()->addItem(ItemFactory::getInstance()->get(2001));
            ////                      TODO add new mask code to go here!
            //                        $p->getInventory()->addItem(VanillaItems::LEATHER_CAP());//TEMP!!!!!!!!
            //						break;
            //					case 1:
            //						$this->sendMessage($p, "§aUsed Legendary Key! §fYou got a §e§lWarden Mask!\n§b§lBuy Crate Keys at shop.fallentech.io for §aEPIC REWARDS!");
            ////						$p->getInventory()->addItem(ItemFactory::getInstance()->get(2002));
            ////                      TODO add new mask code to go here!
            //                        $p->getInventory()->addItem(VanillaItems::LEATHER_CAP());//TEMP!!!!!!!!
            //						break;
            //					case 2:
            //						$this->sendMessage($p, "§aUsed Legendary Key! §fYou got a §e§lEnder Dragon Mask!\n§b§lBuy Crate Keys at shop.fallentech.io for §aEPIC REWARDS!");
            ////						$p->getInventory()->addItem(ItemFactory::getInstance()->get(2003));
            ////                      TODO add new mask code to go here!
            //                        $p->getInventory()->addItem(VanillaItems::LEATHER_CAP());//TEMP!!!!!!!!
            //						break;
            //					case 3:
            //						$this->sendMessage($p, "§aUsed Legendary Key! §fYou got a §e§lEnderman Mask!\n§b§lBuy Crate Keys at shop.fallentech.io for §aEPIC REWARDS!");
            ////						$p->getInventory()->addItem(ItemFactory::getInstance()->get(2004));
            ////                      TODO add new mask code to go here!
            //                        $p->getInventory()->addItem(VanillaItems::LEATHER_CAP());//TEMP!!!!!!!!
            //						break;
            //				}
            //				break;
            case 15:
                $this->sendMessage($p, "§eUsed Legendary Key! §fYou got a §bMystic §fKey!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getCrateKeys('mystic');
                $p->getInventory()->addItem($item);
                break;
            case 16:
                $this->sendMessage($p, "§eUsed Legendary Key! §fYou got a Carver Scroll!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getScrolls('carver'));
                break;
            case 17:
                switch (mt_rand(1, 5)) {
                    case 1:
                    case 2:
                        $this->sendMessage($p, "§eUsed Legendary Key! §fYou got §dNetherite Pickaxe! \n§b§lBuy Crate Keys at shop.fallentech.io for§a EPIC REWARDS!");
                        //                        $item = ItemFactory::getInstance()->get(745);
                        $item = VanillaItems::NETHERITE_PICKAXE();
                        $p->getInventory()->addItem($item);
                        break;
                    case 3:
                    case 4:
                        $this->sendMessage($p, "§eUsed Legendary Key! §fYou got §dNetherite Shovel! \n§b§lBuy Crate Keys at shop.fallentech.io for§a EPIC REWARDS!");
                        //                        $item = ItemFactory::getInstance()->get(744);
                        $item = VanillaItems::NETHERITE_SHOVEL();
                        $p->getInventory()->addItem($item);
                        break;
                    case 5:
                        $this->sendMessage($p, "§eUsed Legendary Key! §fYou got §dNetherite Axe! \n§b§lBuy Crate Keys at shop.fallentech.io for§a EPIC REWARDS!");
                        //                        $item = ItemFactory::getInstance()->get(746);
                        $item = VanillaItems::NETHERITE_AXE();
                        $p->getInventory()->addItem($item);
                        break;
                }
                break;
            default:
                $this->sendMessage($p, "§eUsed Legendary Key! §fYou got §e§lNothing, try again!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                break;
        }
    }

    /**
     * @param SBPlayer $p
     */
    public function checkMysticKey(Player $p) : void {
        switch (mt_rand(1, 25)) {
            case 1:
                $this->sendMessage($p, "§dUsed Mystic Key! §fCheck your Inventory, you got§e§l Golden Enchanted Apples!\n§b§lBuy Mystic Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(466, 0, mt_rand(3, 5));
                $item = VanillaItems::ENCHANTED_GOLDEN_APPLE()->setCount(mt_rand(3, 5));
                $p->getInventory()->addItem($item);
                break;
            case 2:
                //				$this->sendMessage($p, "§dUsed Mystic Key! §fCheck your Inventory, you got§e§l Bedrock!\n§b§lBuy Mystic Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                ////                $item = ItemFactory::getInstance()->get(7, 0, mt_rand(50, 64));
                //				$item = VanillaBlocks::BEDROCK()->asItem()->setCount(mt_rand(50, 64));
                //				$p->getInventory()->addItem($item);
                break;
            case 3:
                $this->sendMessage($p, "§dUsed Mystic Key! §fCheck your Inventory, you got §l§eDiamonds!\n§b§lBuy Mystic Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(264, 0, mt_rand(50, 55));
                $item = VanillaItems::DIAMOND()->setCount(mt_rand(50, 55));
                $p->getInventory()->addItem($item);
                break;
            case 4:
                $this->sendMessage($p, "§dUsed Mystic Key! §fCheck your Inventory, you got §l§eLapis Lazuli!\n§b§lBuy Mystic keys at shop.fallentech.io for§a EPIC REWARDS!");
                //                $item = ItemFactory::getInstance()->get(351, 4, mt_rand(45, 55));
                $item = VanillaItems::LAPIS_LAZULI()->setCount(mt_rand(45, 55));
                $p->getInventory()->addItem($item);
                break;
            case 5:
                $this->sendMessage($p, "§dUsed Mystic Key! §fCheck your Inventory, you got §l§bExclusive Book!\n§b§lBuy Mystic keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getCEBook('exclusive');
                $p->getInventory()->addItem($item);
                break;
            case 6:
                $this->sendMessage($p, "§dUsed Mystic Key! §fYou got a LevelUp Scroll!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getScrolls('levelup');
                $p->getInventory()->addItem($item);
                break;
            case 7:
                $this->sendMessage($p, "§dUsed Mystic Key! §fYou got a Enchanter Scroll!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getScrolls('enchanter');
                $p->getInventory()->addItem($item);
                break;
            case 8:
                $this->sendMessage($p, "§dUsed Mystic Key! §fYou got 700 Casino chips! Check by /mychips\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $user = $this->pl->getUserManager()->getOnlineUser($p->getName());
                $user->addChips(700);
                break;
            case 9:
                $this->sendMessage($p, "§dUsed Mystic Key! §fYou got an OP Sword!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getFunctions()->opSword(mt_rand(4, 6)));
                break;
            case 10:
                //            case 11:
                //				$i = mt_rand(1, 4);
                //				switch ($i) {
                //					case 0:
                //						$this->sendMessage($p, "§aUsed Mystic Key! §fYou got a §e§lBlood Dragon Mask!\n§b§lBuy Crate Keys at shop.fallentech.io for §aEPIC REWARDS!");
                ////						$p->getInventory()->addItem(ItemFactory::getInstance()->get(2001));
                ////                      TODO add new mask code to go here!
                //                        $p->getInventory()->addItem(VanillaItems::LEATHER_CAP());//TEMP!!!!!!!!
                //						break;
                //					case 1:
                //						$this->sendMessage($p, "§aUsed Mystic Key! §fYou got a §e§lWarden Mask!\n§b§lBuy Crate Keys at shop.fallentech.io for §aEPIC REWARDS!");
                ////						$p->getInventory()->addItem(ItemFactory::getInstance()->get(2002));
                ////                      TODO add new mask code to go here!
                //                        $p->getInventory()->addItem(VanillaItems::LEATHER_CAP());//TEMP!!!!!!!!
                //						break;
                //					case 2:
                //						$this->sendMessage($p, "§aUsed Mystic Key! §fYou got a §e§lEnder Dragon Mask!\n§b§lBuy Crate Keys at shop.fallentech.io for §aEPIC REWARDS!");
                ////						$p->getInventory()->addItem(ItemFactory::getInstance()->get(2003));
                ////                      TODO add new mask code to go here!
                //                        $p->getInventory()->addItem(VanillaItems::LEATHER_CAP());//TEMP!!!!!!!!
                //						break;
                //					case 3:
                //						$this->sendMessage($p, "§aUsed Mystic Key! §fYou got a §e§lEnderman Mask!\n§b§lBuy Crate Keys at shop.fallentech.io for §aEPIC REWARDS!");
                ////						$p->getInventory()->addItem(ItemFactory::getInstance()->get(2004));
                ////                      TODO add new mask code to go here!
                //                        $p->getInventory()->addItem(VanillaItems::LEATHER_CAP());//TEMP!!!!!!!!
                //						break;
                //				}
                //				break;
            case 12:
                $gkit = array_rand($this->pl->gkits, 1);
                $this->sendMessage($p, "§dUsed Mystic Key! §fCheck your gkits by /gkit, you got $gkit §fgkit access!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $user = $this->pl->getUserManager()->getOnlineUser($p->getName());
                $user->addKitCount($gkit);
                break;
            case 13:
            case 14:
            case 15:
                $this->sendMessage($p, "§dUsed Mystic Key! §fCheck your inventory for a GKit tool\armor!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getFunctions()->gkitItems(mt_rand(1, 6), 'random');
                $p->getInventory()->addItem($item);
                break;
            case 16:
            case 17:
                $this->sendMessage($p, "§dUsed Mystic Key! §fCheck your inventory, you got a GOD Scroll!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getScrolls('god');
                $p->getInventory()->addItem($item);
                break;
            case 18:
            case 19:
                $this->sendMessage($p, "§dUsed Mystic Key! §fCheck your inventory, you got a Tag!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getTagManager()->getRandomTag());
                break;
            case 20:
            case 21:
                $this->sendMessage($p, "§dUsed Mystic Key! §fCheck your inventory, you got a Inferno Scroll!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $item = $this->pl->getScrolls('inferno');
                $p->getInventory()->addItem($item);
                break;
            case 22:
                $this->sendMessage($p, "§dUsed Mystic Key! §fYou got a Vulcan Scroll!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getScrolls('vulcan'));
                break;
            case 23:
                $this->sendMessage($p, "§dUsed Mystic Key! §fYou got a Carver Scroll!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                $p->getInventory()->addItem($this->pl->getScrolls('carver'));
                break;
            case 24:
                switch (mt_rand(1, 5)) {
                    case 1:
                    case 2:
                        $this->sendMessage($p, "§dUsed Mystic Key! §fYou got §dNetherite Pickaxe! \n§b§lBuy Crate Keys at shop.fallentech.io for§a EPIC REWARDS!");
                        $item = VanillaItems::NETHERITE_PICKAXE();
                        $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::UNBREAKING), mt_rand(1, 5)));
                        $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::EFFICIENCY), mt_rand(1, 4)));
                        $p->getInventory()->addItem($item);
                        break;
                    case 3:
                    case 4:
                        $this->sendMessage($p, "§dUsed Mystic Key! §fYou got §dNetherite Shovel! \n§b§lBuy Crate Keys at shop.fallentech.io for§a EPIC REWARDS!");
                        $item = VanillaItems::NETHERITE_SHOVEL();
                        $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::UNBREAKING), mt_rand(1, 5)));
                        $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::EFFICIENCY), mt_rand(1, 4)));
                        $p->getInventory()->addItem($item);
                        break;
                    case 5:
                        $this->sendMessage($p, "§dUsed Mystic Key! §fYou got §dNetherite Axe! \n§b§lBuy Crate Keys at shop.fallentech.io for§a EPIC REWARDS!");
                        $item = VanillaItems::NETHERITE_AXE();
                        $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::UNBREAKING), mt_rand(1, 5)));
                        $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::EFFICIENCY), mt_rand(1, 4)));
                        $p->getInventory()->addItem($item);
                        break;
                }
                break;
            default:
                $this->sendMessage($p, "§dUsed Mystic Key! §fYou got §e§lNothing, try again!\n§b§lBuy Crate keys at shop.fallentech.io for§a EPIC REWARDS!");
                break;
        }
    }

    public function randomizeWithPercentage($array) {
        $totalWeight = array_sum($array);
        $rand = rand(1, $totalWeight);

        $currentWeight = 0;
        foreach ($array as $key => $weight) {
            $currentWeight += intval($weight);
            if ($rand <= $currentWeight) {
                return $key;
            }
        }
        return array_values($array)[0];
    }

    public function checkCeKey(Player $p, $safeguard = 0) : void {
        if ($safeguard > 100) {
            //a catch if this loops over 100 times somehow
            $p->sendMessage("An Error occurred with that item!");
        }
        $enchants = $this->pl->getEnchantFactory()->getEnchants();
        $key = array_keys($enchants)[mt_rand(0, sizeof($enchants) - 1)];
        if ($this->pl->isVaulted($key)) {
            $this->checkCeKey($p, $safeguard + 1);
            return;
        }
        $item = null;
        $enchant = BaseEnchantment::getEnchantment($key);
        $itemType = $enchants[$key][2];
        /** First number is level and second number is % */
        $levelPercentage = [
            11 => 60,
            12 => 25,
            13 => 10,
            14 => 8,
            15 => 2
        ];
        $allowed = [
            BaseEnchantment::ITEM_TYPE_PICKAXE,
            BaseEnchantment::ITEM_TYPE_SWORD,
            BaseEnchantment::ITEM_TYPE_AXE,
            BaseEnchantment::ITEM_TYPE_TOOLS,
            BaseEnchantment::ITEM_TYPE_HELMET,
            BaseEnchantment::ITEM_TYPE_CHESTPLATE,
            BaseEnchantment::ITEM_TYPE_LEGGINGS,
            BaseEnchantment::ITEM_TYPE_BOOTS,
            BaseEnchantment::ITEM_TYPE_ARMOR,
        ];
        $lvl = $this->randomizeWithPercentage($levelPercentage);
        if (in_array($itemType, $allowed)) {
            $enchantInstance = new EnchantmentInstance($enchant, $lvl);
            switch ($itemType) {
                case BaseEnchantment::ITEM_TYPE_SWORD:
                    $item = VanillaItems::DIAMOND_SWORD();
                    break;
                case BaseEnchantment::ITEM_TYPE_PICKAXE:
                    $item = [VanillaItems::DIAMOND_PICKAXE(), VanillaItems::NETHERITE_PICKAXE()][mt_rand(0, 1)];
                    break;
                case $itemType === BaseEnchantment::ITEM_TYPE_AXE:
                    $item = [VanillaItems::DIAMOND_AXE(), VanillaItems::NETHERITE_AXE()][mt_rand(0, 1)];
                    break;
                case BaseEnchantment::ITEM_TYPE_TOOLS:
                    $item = [VanillaItems::DIAMOND_AXE(), VanillaItems::NETHERITE_AXE(), VanillaItems::DIAMOND_PICKAXE(), VanillaItems::NETHERITE_PICKAXE()][mt_rand(0, 3)];
                    break;
                case BaseEnchantment::ITEM_TYPE_ARMOR:
                    $item = [VanillaItems::DIAMOND_HELMET(), VanillaItems::DIAMOND_CHESTPLATE(), VanillaItems::DIAMOND_LEGGINGS(), VanillaItems::DIAMOND_BOOTS()][mt_rand(0, 3)];
                    break;
                case BaseEnchantment::ITEM_TYPE_HELMET:
                    $item = VanillaItems::DIAMOND_HELMET();
                    break;
                case BaseEnchantment::ITEM_TYPE_CHESTPLATE:
                    $item = VanillaItems::DIAMOND_CHESTPLATE();
                    break;
                case BaseEnchantment::ITEM_TYPE_LEGGINGS:
                    $item = VanillaItems::DIAMOND_LEGGINGS();
                    break;
                case BaseEnchantment::ITEM_TYPE_BOOTS:
                    $item = VanillaItems::DIAMOND_BOOTS();
                    break;
            }
            if ($item === null) $this->checkCeKey($p, $safeguard + 1); //if $item is still somehow null when it gets here just re-run it
            $item->addEnchantment($enchantInstance);
            $item = $this->pl->getFunctions()->setEnchantmentNames($item, false);
            $this->sendMessage($p, "§9Used §l§7§k:§r§l§9CE§7§k:§r §l§9Key§r§9!§r You got §b" . $enchants[$key][0] . " $lvl §fon a §b" . $item->getVanillaName() . "§r!");
            $p->getInventory()->addItem($item);
        } else {
            $this->checkCeKey($p, $safeguard + 1);
        }
    }

    public function checkVeKey(Player $p) : void {
        $item = CustomItems::ENDER_EYE();
        $enchs = [0 => "Protection", 1 => "FireProtection", 2 => "FeatherFalling", 3 => "BlastProtection", 4 => "ProjectileProtection", 6 => "Respiration", 7 => "DepthStrider", 8 => "AquaAffinity", 9 => "Sharpness", 10 => "Smite", 11 => "BaneOfArthropods", 12 => "Knockback", 13 => "FireAspect", 15 => "Efficiency", 16 => "SilkTouch", 17 => "Unbreaking", 18 => "Fortune", 22 => "Infinity"];
        $rand_key = array_rand($enchs, 1);
        $level = mt_rand(11, 15);
        $item->setCustomName(" §r§l§6{$enchs[$rand_key]} §r§9Enchantment Orb \n §aLevel: §6$level \n §3ID: §6{$rand_key} \n §eUse this on a tool or armor by /ench ");
        $this->sendMessage($p, "§cUsed §l§7§k:§r§l§cVE§7§k:§r §l§cKey§c!§r You got a §b{$enchs[$rand_key]} §b $level §rEnchantment Orb!");
        $p->getInventory()->addItem($item);
    }

    /**
     * @param SBPlayer $player
     */
    public function setTime(Player $player) : void {
        $time = Values::COMBAT_TIME;
        $msg = "§c§oYou are now in Combat Mode! Logging out now will cause you to die.\n§7Please wait $time seconds before logging out.§r";
        if (isset($this->pl->combat[$player->getName()])) {
            if ($this->pl->combat[$player->getName()] < time()) {
                $player->sendMessage($msg);
            }
        } else {
            $player->sendMessage($msg);
        }
        $this->pl->combat[$player->getName()] = time() + $time;
    }

    /**
     * @param string $group
     *
     * @return string
     */
    public function getColor(string $group) : string {
        if ($this->pl->chatpack) {
            return match (strtolower($group)) {
                "guest" => "§fGuest",
                "king" => "\u{E0AA}",
                "vip" => "\u{E0AB}",
                "myth" => "\u{E0AC}",
                "skylord" => "\u{E0AD}",
                "skygod" => "\u{E0AE}",
                "skyzeus" => "§l§k§a:\u{E0AF}:§r",
                "skyelite" => "§l§k§a::\u{E0B2}::§r",
                "skyhulk" => "§l§k§a::\u{E0B3}::§r",
                "skywarrior" => "§l§k§a::\u{E0B4}\u{E0B5}::§r",
                default => '',
            };
        }
        return match (strtolower($group)) {
            "guest" => '§fGuest',
            "king" => '§aKing',
            "vip" => '§bVIP',
            "myth" => '§dMyth',
            "skylord" => '§9SkyLord',
            "skygod" => '§9Sky§6GOD',
            "skyzeus" => '§l§k§a|§r§9Sky§cZE§bUS§l§k§a|§r',
            "skyelite" => '§l§k§a||§r§9Sky§bELI§6TE§l§k§a||§r',
            "skyhulk" => '§l§k§a||§r§l§9Sky§aHULK§l§k§a||§r',
            "skywarrior" => '§l§k§a||§r§o§l§dSky§bWarrior§r§l§k§a||§r',
            default => '',
        };
    }

    /**
     * @param string $player
     *
     * @return bool
     */
    public function hasStaffRank(string $player) : bool {
        return $this->pl->staffapi->hasStaffRank($player);
    }

    /**
     * @param string $playerName
     *
     * @return bool
     */
    public function hasPremiumRank(string $playerName) : bool {
        if ($this->hasStaffRank($playerName)) return true;
        return $this->pl->permsapi->getUserGroup($playerName)->getName() !== "Guest";
    }

    /**
     * @param SBPlayer $player
     */
    public function renderNameTag(Player $player) : void {
        $rank = $this->pl->permsapi->getUserGroup($player->getName())->getName();
        if (($user = $this->pl->getUserManager()->getOnlineUser($player->getName())) !== null) {
            $player->setNameTag("§b<§6<§b< §l{$this->getColor($rank)}{$this->pl->staffapi->getSetRankColor($player->getName())} §a" . $player->getName() . $user->getSetTag() . $user->getSetGang() . " §r§b>§6>§b>" . "\n§l§f" . (int) $player->getHealth() . TF::RED . "HP" . TF::WHITE . "/" . $player->getMaxHealth() . TF::RED . "HP" . $user->getSetBounty() . " " . TextFormat::BOLD . TextFormat::AQUA . $this->pl->getPlayerOS($player->getName()));
        }
    }

    /**
     * @param SBPlayer $player
     * @param bool     $broadcastJoin
     *
     * @return string
     */
    public function getNametag(Player $player, bool $broadcastJoin = true) : string {
        if (($user = $this->pl->getUserManager()->getOnlineUser($player->getName())) === null) return "";

        $group = $this->pl->permsapi->getUserGroup($player->getName())->getName();
        if ($broadcastJoin) {
            if ($user->getPref()->capes_enabled) $this->checkCape($player);
            if (!$this->pl->staffapi->isSoftStaff($player->getName())) {
                $msg = "➼§7[§a+§7] §a{$player->getName()} §ejoined the game!";
                if ($user->getPref()->welcome_msg) {
                    Server::getInstance()->broadcastMessage($msg);
                } else {
                    $this->sendStaffMessage($msg);
                }
            }
        }
        return "§b<§6<§b< §l{$this->getColor($group)}{$this->pl->staffapi->getSetRankColor($player->getName())} §e{$player->getName()}{$user->getSetTag()}{$user->getSetGang()} §r§b>§6>§b>" . "\n" . TextFormat::BOLD . TextFormat::AQUA . $this->pl->getPlayerOS($player->getName());
    }


    /**
     * @param string $msg
     */
    public function sendStaffMessage(string $msg) : void {
        foreach ($this->pl->getServer()->getOnlinePlayers() as $player) {
            if ($this->pl->staffapi->isSoftStaff($player->getName())) {
                $player->sendMessage($msg);
            }
        }
    }

    /**
     * @param SBPlayer $player
     * @param Player   $recipient
     *
     * @return string|null
     */
    public function getNormalChatFormat(Player $player, Player $recipient) : ?string {
        $group = $this->pl->permsapi->getUserGroup($player->getName())->getName();
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $recipientUser = $this->pl->getUserManager()->getOnlineUser($recipient->getName());
        if ($user === null || $recipientUser === null) {
            return null;
        }
        $name = $player->getName();
        if ($this->pl->staffapi->hasStaffRank($name)) $name = "§l" . $player->getName() . "§r";

        $arrow2 = TextFormat::BOLD . $this->getArrow2($group, $player->getName()) . TextFormat::RESET;
        $playerRank = TextFormat::GRAY . "[" . $this->getColor($group) . TextFormat::GRAY . "]" . TextFormat::RESET;
        $staffRank = $this->pl->staffapi->getSetRankColor($player->getName());
        $tag = $user->getSetTag();
        $gang = $user->getSetGang();
        $arrow = $this->getArrow($group, $player->getName()) . " ";
        $os = Main::getInstance()->getPlayerOS($player->getName());

        if ($user->isIslandSet()) {
            $islandLevel = TextFormat::WHITE . TextFormat::BOLD . "`" . $this->getIslandLevel($user->getIsland()) . "`" . TextFormat::RESET;
            $islandRank = TextFormat::WHITE . $user->getIslandRankInStars();
            $islandName = " " . TextFormat::DARK_AQUA . $user->getIsland() . TextFormat::RESET;

            $message = $arrow2;
            if ($recipientUser->getPref()->showIslandLevel) {
                $message .= $islandLevel;
            }
            if ($recipientUser->getPref()->showIslandRank) {
                $message .= $islandRank;
            }
            if ($recipientUser->getPref()->showIslandName) {
                $message .= $islandName;
            }

        } else {
            $message = $arrow2;

        }
        if ($recipientUser->getPref()->showRanks) {
            $message .= " " . $playerRank;
            $message .= $staffRank;
            $message .= " " . TextFormat::GREEN . $name;
        } else {
            if ($staffRank == "") {
                $message .= " " . $this->getRankColorNotShowingForChat($group) . $name;
            } else {
                $message .= " " . $staffRank . " " . $this->getRankColorNotShowingForChat($group) . $name;
            }
        }
        if ($recipientUser->getPref()->showOS) {
            $message .= TextFormat::RESET . " [" . TextFormat::AQUA . $os . TextFormat::RESET . "]";
            $os = true;
        }
        if ($recipientUser->getPref()->showTags) {
            $message .= TextFormat::RESET . ($recipientUser->getPref()->showOS === true ? " " : "") . $tag . TextFormat::RESET;
        }
        if ($recipientUser->getPref()->showGangs) {
            $message .= TextFormat::RESET . ($recipientUser->getPref()->showTags === true ? " " : "") . $gang . TextFormat::RESET;
        }
        $message .= ($recipientUser->getPref()->showGangs === true ? " " : "") . $arrow;
        return $message;
        //		$group = $this->pl->permsapi->getUserGroup($player->getName())->getName();
        //		$user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        //		$name = $player->getName();
        //		if ($this->pl->staffapi->hasStaffRank($name)) $name = "§l" . $player->getName() . "§r";
        //		var_dump($user->getPref()->showIslandLevel);
        //		if ($user->isIslandSet())
        //			return "§l{$this->getArrow2($group, $player->getName())}§r`{$this->getIslandLevel($user->getIsland())}§r` §f{$user->getIslandRankInStars()} §3{$user->getIsland()} §r§7[{$this->getColor($group)}§7]{$this->pl->staffapi->getSetRankColor($player->getName())} §a{$name}{$user->getSetTag()}{$user->getSetGang()} {$this->getArrow($group, $player->getName())} ";
        //		else
        //			return "§l{$this->getArrow2($group, $player->getName())}§r§7[{$this->getColor($group)}§7]{$this->pl->staffapi->getSetRankColor($player->getName())} §a{$name}{$user->getSetTag()}{$user->getSetGang()} {$this->getArrow($group, $player->getName())} ";

    }

    public function getRankColorNotShowingForChat(string $rank) : string {
        return match (strtolower($rank)) {
            "king" => TextFormat::GREEN,
            "vip" => TextFormat::AQUA,
            "myth" => TextFormat::LIGHT_PURPLE,
            "skylord" => TextFormat::BLUE,
            "skygod" => TextFormat::YELLOW,
            "skyzeus" => TextFormat::RED,
            "skyelite" => TextFormat::GOLD,
            "skyhulk" => TextFormat::DARK_GREEN,
            "skywarrior" => TextFormat::DARK_PURPLE,
            default => TextFormat::WHITE,
        };
    }

    /**
     * @param string $group
     * @param string $name
     *
     * @return string
     */
    public function getArrow2(string $group, string $name) : string {
        $i = '';
        if ($this->pl->chatpack) {
            if ($this->hasStaffRank($name)) {
                if (isset($this->pl->chatsize[strtolower($name)])) {
                    $i = '';
                } else {
                    $i = '§f§f§f';
                }
                return $i;
            }
            switch (strtolower($group)) {
                case "guest":
                case "king":
                case "vip":
                case "myth":
                case "skylord":
                case "skygod":
                case "skyzeus":
                case "skyelite":
                    $i = '§f§f§f';
                    break;
                case "skyhulk":
                case "skywarrior":
                    if (isset($this->pl->chatsize[strtolower($name)])) {
                        $i = '';
                    } else {
                        $i = '§f§f§f';
                    }
                    break;
            }
        } else {
            if ($this->hasStaffRank($name)) {
                if (isset($this->pl->chatsize[strtolower($name)])) {
                    $i = '';
                } else {
                    $i = '§f➼';
                }
                return $i;
            }
            switch (strtolower($group)) {
                case "guest":
                    $i = '§f➼';
                    break;
                case "king":
                    $i = '§a➼';
                    break;
                case "vip":
                    $i = '§b➼';
                    break;
                case "myth":
                    $i = '§d➼';
                    break;
                case "skylord":
                    $i = '§9➼';
                    break;
                case "skygod":
                    $i = '§6➼';
                    break;
                case "skyzeus":
                    $i = '§c➼';
                    break;
                case "skyelite":
                    $i = '§5➼';
                    break;
                case "skyhulk":
                case "skywarrior":
                    if (isset($this->pl->chatsize[strtolower($name)])) {
                        $i = '';
                    } else {
                        $i = '§f➼';
                    }
                    break;
            }
        }
        return $i;
    }

    /**
     * @param string $island
     *
     * @return string
     */
    public function getIslandLevel(string $island) : string {
        if (($island = $this->pl->getIslandManager()->getOnlineIsland($island)) === null) return '';
        $level = $island->getLevel();
        $level = $level . '';
        $color = ['b', 'e', 'a', 'd'];
        $i = '';
        if ($level > 1000) {
            for ($y = 0; $y < strlen($level); $y++) {
                $key = mt_rand(0, 3);
                $i .= "§{$color[$key]}{$level[$y]}";
            }
            return $i;
        }
        if ($level > 500) {
            for ($y = 0; $y < strlen($level); $y++) {
                $i .= "§{$color[$y]}{$level[$y]}";
            }
            return $i;
        }
        if ($level > 250) {
            return "§b$level";
        }
        if ($level > 175) {
            return "§e$level";
        }
        if ($level > 100) {
            return "§a$level";
        }
        if ($level > 50) {
            return "§c$level";
        }
        if ($level > 25) {
            return "§6$level";
        }
        if ($level > 15) {
            return "§4$level";
        }
        if ($level > 1) {
            return "§f$level";
        }
        if ($level > 0) {
            return "§7$level";
        }
        return $i;
    }

    /**
     * @param string $group
     * @param string $name
     *
     * @return string
     */
    public function getArrow(string $group, string $name) : string {
        if ($this->pl->staffapi->hasStaffRank($name)) return '§f>§r§e';
        return match (strtolower($group)) {
            "guest" => '§f>',
            "king" => '§a»§r§a',
            "vip" => '§b»§r§a',
            "myth" => '§d»§r§a',
            "skylord" => '§9»§r§a',
            "skygod" => '§6»§r§a',
            "skyzeus" => '§c»§r§a',
            "skyelite" => '§5»§r§a',
            "skyhulk", "skywarrior" => '§f»§r§a',
            default => "",
        };
    }

    /**
     * @param array    $words
     * @param Item     $item
     * @param SBPlayer $player
     *
     * @return bool
     */
    public function checkKitEffects(array $words, Item $item, Player $player) : bool {
        $inventory = $player->getInventory();
        foreach ($words as $w) {
            $w = TextFormat::clean($w);
            $kits = $this->pl->getKit($w);
            if ($kits !== null) {
                $name = $kits->getName();
                if (isset($kits->data["effects"])) {
                    foreach ($kits->data["effects"] as $effectString) {
                        $e = $kits->loadEffect(...explode(":", $effectString));
                        if ($e !== null)
                            $player->getEffects()->add($e);
                    }
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $this->sendMessage($player, "§eSuccessfully used §a{$name} §eEffects!");
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param array  $words
     * @param Player $player
     * @param string $itemName
     *
     * @return void
     */
    public function checkKothWinner(array $words, Player $player, string $itemName) : void {
        if ($words[0] == '§l§bKOTH') {
            $lines = preg_split("/(\r\n|\n|\r)/", $itemName);
            $winner = trim($lines[1]);
            $number = $words[array_key_last($words)];
            $this->sendMessage($player, "§eWinner of §bKOTH $number §ewas §a$winner");
        }
    }

    /**
     * @param array    $words
     * @param Item     $item
     * @param SBPlayer $player
     */
    public function checkXP(array $words, Item $item, Player $player) : void {
        $inventory = $player->getInventory();
        foreach ($words as $w) {
            $w = TextFormat::clean($w);
            $kits = $this->pl->getKit($w);
            if ($kits !== null) {
                if (isset($kits->data["xp"])) {
                    $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                    Functions::safeXPAdd($user, $kits->data["xp"]);
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $this->sendMessage($player, "§eSuccessfully claimed §a{$kits->data["xp"]} §eXP!");
                }
            }
        }
    }

    /**
     * @param array    $words
     * @param Item     $item
     * @param SBPlayer $player
     */
    public function checkKitChips(array $words, Item $item, Player $player) : void {
        $inventory = $player->getInventory();
        foreach ($words as $w) {
            $w = TextFormat::clean($w);
            $kits = $this->pl->getKit($w);
            if ($kits !== null) {
                if (isset($kits->data["chips"])) {
                    $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                    $user->addChips($kits->data["chips"]);
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $this->sendMessage($player, "§eSuccessfully added §a{$kits->data["chips"]} §eChips!");
                }
            }
        }
    }

    /**
     * @param array    $words
     * @param Item     $item
     * @param SBPlayer $player
     */
    public function checkCheque(array $words, Item $item, Player $player) : void {
        $inventory = $player->getInventory();
        foreach ($words as $w) {
            if ($w == '§aCheque') {
                [, $money] = explode(' §eMoney: ', $item->getCustomName());
                $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                if (is_int((int) $money)) {
                    $money = (int) $money;
                    $user->addMoney($money);
                    $item->setCount($item->getCount() - 1);
                    $inventory->setItemInHand($item);
                    $this->sendMessage($player, "§eSuccessfully redeemed §6{$money}$!");
                }
            }
        }
    }

    /**
     * @param SBPlayer $player
     * @param string   $type
     */
    public static function addCape(Player $player, string $type) : void {
        if ($type === 'none') {
            $capedata = "";
        } else {
            $capedata = Main::$capes[$type] ?? null;
        }
        if ($capedata === null) return;
        $skin = $player->getSkin();
        try {
            $player->setSkin(new Skin($skin->getSkinId(), $skin->getSkinData(), $capedata, $skin->getGeometryName(), $skin->getGeometryData()));
        } catch (JsonException) {
        }
        $player->sendSkin();
    }

    /**
     * @param SBPlayer $player
     */
    public function checkCape(Player $player) : void {
        if ($this->pl->staffapi->isSoftStaff($player->getName())) {
            self::addCape($player, "Staff");
        } else {
            if (($rank = strtolower($this->pl->getRank($player))) === "guest") {
                self::addCape($player, "FT");
            } else {
                self::addCape($player, $rank);
            }
        }
    }

    public function mcmmoKillBoost(User $user) : int {
        $level = $user->getLevel("combat");
        return (int) (floor($level / 10) * 3);
    }

    public function mcmmoGamblingBoost(User $user) : int {
        $level = $user->getLevel("gambling");
        return (int) (floor($level / 3) * 3);
    }

    /**
     * @param SBPlayer $killer
     * @param SBPlayer $p
     */
    public function killReward(Player $killer, Player $p) : void {
        $user = $this->pl->getUserManager()->getOnlineUser($p->getName());
        if (($bounty = $user->getBounty()) > 0) $user->setBounty(0);
        $user->addDeath();
        $user2 = $this->pl->getUserManager()->getOnlineUser($killer->getName());
        if ($bounty > 0) {
            $this->sendMessage($killer, "§eYou claimed §6" . number_format($bounty) . "$ §ebounty from killing §a{$p->getDisplayName()}");
            $user2->addMoney($bounty, false);
        }
        $user2->setPoints(mt_rand(25, 100), "combat");
        if ($user->getStreak() != 0) $this->sendMessage($user->getPlayer(), "Oh no! §cYou lost your streak of §d{$user->getStreak()}§c!");
        $user->removeStreak();
        $user2->addKill();
        $user2->addMana($wmana = mt_rand(5, 10));
        $user->removeMana($lmana = mt_rand(10, 15));
        //        $head = ItemFactory::getInstance()->get(397, 3, 1);
        $head = CustomItems::SKULL();
        //        if ($killer->getInventory()->canAddItem($head)) $killer->getInventory()->addItem($head->setCustomName(TF::RESET . " §a{$p->getDisplayName()}§6's Head \n §eUse /headsell to sell this head "));
        $this->sendMessage($killer, "§eAdded §b$wmana §emana!");
        $this->sendMessage($p, "§cLost §b$lmana §emana!");
        $user2->addStreak();
        $streak = $user2->getStreak();
        if ($streak > 1)
            $this->sendMessage($killer, "§eYou're on a kill streak of §d$streak");
        if ($streak != 0 && $streak % 10 == 0)
            $this->pl->getServer()->broadcastMessage(TF::GREEN . TF::BOLD . "[" . TF::AQUA . "FT" . TF::GREEN . "]> " . TF::RESET . "§a{$killer->getDisplayName()} §eis on a killstreak of §d{$streak}§e!");
        $killmsg = str_replace(["{player1}", "{player2}"], [$p->getDisplayName(), $killer->getDisplayName() . "§7[§f" . (int) $killer->getHealth() . "§cHP§7]§r" . Util::getNameOfItem($killer->getInventory()->getItemInHand(), "", [BlockTypeIds::MOB_HEAD], "§b's §a")], $this->pl->deathmessages[mt_rand(0, count($this->pl->deathmessages) - 1)]);
        Server::getInstance()->broadcastMessage("§l§6➼§c[Kill]§r§a> " . $killmsg);
        $this->pl->sendKillLog($p->getDisplayName(), $killer->getDisplayName());
        if ($user2->hasGang()) {
            $gang = $user2->getGang();
            $gangc = $this->pl->getGangManager()->getOnlineGang($gang);
            $gangc->addMemberKill($killer->getName());
            $gangc->setPoints(25);
            $level = $gangc->getLevel();
            $mul = 20;
            $user2->addMoney($money = $mul * $level);
            $this->sendMessage($killer, "§6You get $mul x {$level} = §6$money$ for your gang level!");
        }
        if ($user->hasGang()) {
            $gang = $user->getGang();
            $gangc = $this->pl->getGangManager()->getOnlineGang($gang);
            $gangc->addMemberDeath($p->getName());
            $gangc->setPoints(-30);
        }
        $boosters = [15, 12, 10, 5, 3, 2];
        $start = 200;
        foreach ($boosters as $mul) {
            if ($killer->hasPermission("kill.$mul")) {
                $mcboost = $this->mcmmoKillBoost($user2);
                $ogmoney = $start * $mul;
                $incr = ($mcboost / 100) * $ogmoney;
                $money = $ogmoney + $incr;
                $user2->addMoney($money);
                $extra = ($mcboost > 0) ? '§e + MCMMO Boost - ' . $incr . "$ (§d+" . $mcboost . "%§e)" : '';
                $this->sendMessage($killer, "§eYou get $start x $mul = §6$ogmoney$" . $extra . " §bfor killing §a{$p->getDisplayName()}" . "§b! Kill Booster x {$mul}");
                return;
            }
        }
        if ($user->getPref()->capes_enabled) self::addCape($p, 'L');
        if ($user2->getPref()->capes_enabled) self::addCape($killer, 'W');
        unset($this->pl->combat[$p->getName()]);
        //        unset($this->pl->combat[$killer->getName()]);
        $user2->addMoney($start);
        $this->sendMessage($killer, "§eYou get §6$start$ §bfor killing §a{$p->getDisplayName()}");
    }

    /**
     * @param SBPlayer $p
     * @param string   $ce
     */
    public function checkCEBook(Player $p, string $ce) : void {
        if (($edata = $this->pl->getEnchantFactory()->getEnchantmentByName($ce)) !== null) {
            $name = $edata[0];
            $rarity = $edata[3];
            $type = $edata[4];
            $info = $edata[5];
            $this->sendMessage($p, "§a$name §e=> §7$info! §eCategory => §a$rarity §dType §e=> §a$type\n§f- Hold a tool and use /combiner to enchant this book with the tool");
        }
    }

    /**
     * @param string   $name
     * @param SBPlayer $player
     *
     * @return string
     */
    public function checkRelic(string $name, Player $player) : string {
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        switch ($name) {
            case "§r§6Common Relic\n§r§fPlace it to claim it":
                $i = mt_rand(1, 100);
                if ($i > 90) {
                    $player->getInventory()->addItem($this->pl->getCEBook('rare'));
                    return "You got a §6Rare §eBook";
                }
                if ($i > 75) {
                    $player->getInventory()->addItem($this->pl->getCrateKeys('common'));
                    return "You got a §6Common §eKey";
                }
                if ($i > 60) {
                    $amount = mt_rand(1, 3);
                    $player->getInventory()->addItem($this->pl->getFunctions()->opSword($amount));
                    return "You got Level §6$amount §aOP Sword";
                }
                if ($i > 40) {
                    $user->addMana($mana = mt_rand(100, 500));
                    return "You get §6$mana §emana";
                }
                if ($i > 10) {
                    $amount = mt_rand(5000, 10000);
                    $user->addMoney($amount);
                    return "You got §6$amount §e$";
                }
                if ($i > 0) {
                    $amount = mt_rand(50, 500);
                    Functions::safeXPAdd($user, $amount);
                    return "You got §6$amount §eXP!";
                }
                break;
            case "§r§aRare Relic\n§r§fPlace it to claim it":
                $i = mt_rand(1, 100);
                if ($i > 90) {
                    $gkit = array_rand($this->pl->gkits, 1);
                    $user->addKitCount($gkit);
                    return "You got a §b$gkit §eGKit";
                }
                if ($i > 75) {
                    $player->getInventory()->addItem($this->pl->getCEBook('legendary'));
                    return "You got a §6Legendary §eBook";
                }
                if ($i > 50) {
                    $player->getInventory()->addItem($this->pl->getCrateKeys('rare'));
                    return "You got a §6Rare §eKey";
                }
                if ($i > 40) {
                    $amount = mt_rand(2, 4);
                    $player->getInventory()->addItem($this->pl->getFunctions()->opSword($amount));
                    return "You got Level §6$amount §aOP Sword";
                }
                if ($i > 30) {
                    $user->addMana($mana = mt_rand(500, 1000));
                    return "You get §6$mana §emana";
                }
                if ($i > 20) {
                    $amount = mt_rand(20000, 45000);
                    $user->addMoney($amount);
                    return "You got §6$amount §e$";
                }
                if ($i > 0) {
                    $amount = mt_rand(250, 1750);
                    Functions::safeXPAdd($user, $amount);
                    return "You got §6$amount §eXP!";
                }
                break;
            case "§r§eLegendary Relic\n§r§fPlace it to claim it":
                $i = mt_rand(1, 100);
                if ($i > 90) {
                    $gkit = array_rand($this->pl->gkits, 1);
                    $user->addKitCount($gkit);
                    return "You got a §b$gkit §eGKit";
                }
                if ($i > 80) {
                    $player->getInventory()->addItem($this->pl->getCrateKeys('legendary'));
                    return "You got a §aLegendary §eKey";
                }
                if ($i > 55) {
                    $player->getInventory()->addItem($this->pl->getCEBook('legendary'));
                    return "You got a §bLegendary §eBook";
                }
                if ($i > 40) {
                    $amount = mt_rand(3, 4);
                    $player->getInventory()->addItem($this->pl->getFunctions()->opSword($amount));
                    return "You got Level §6$amount §aOP Sword";
                }
                if ($i > 30) {
                    $user->addMana($mana = mt_rand(1000, 2500));
                    return "You get §6$mana §emana";
                }
                if ($i > 15) {
                    $amount = mt_rand(30000, 65000);
                    $user->addMoney($amount);
                    return "You got §6$amount §e$";
                }
                if ($i > 0) {
                    $amount = mt_rand(500, 2500);
                    Functions::safeXPAdd($user, $amount);
                    return "You got §6$amount §eXP!";
                }
                break;
            case "§r§d§lMythic Relic\n§r§fPlace it to claim it":
                $i = mt_rand(1, 100);
                if ($i > 95) {
                    $gkit = array_rand($this->pl->gkits, 1);
                    $user->addKitCount($gkit);
                    return "You got a §b$gkit §eGKit";
                }
                if ($i > 90) {
                    $player->getInventory()->addItem($this->pl->getCrateKeys('mystic'));
                    return "You got a §dMystic Crate §eKey";
                }
                if ($i > 80) {
                    $player->getInventory()->addItem($this->pl->getCrateKeys('random', 3));
                    return "You got 3 random §6Crate §eKeys";
                }
                if ($i > 65) {
                    $player->getInventory()->addItem($this->pl->getCEBook('legendary'));
                    return "You got a §aLegendary §eBook";
                }
                if ($i > 50) {
                    $amount = mt_rand(4, 5);
                    $player->getInventory()->addItem($this->pl->getFunctions()->opSword($amount));
                    return "You got Level §6$amount §aOP Sword";
                }
                if ($i > 30) {
                    $user->addMana($mana = mt_rand(1500, 3000));
                    return "You get §6$mana §emana";
                }
                if ($i > 15) {
                    $amount = mt_rand(50000, 100000);
                    $user->addMoney($amount);
                    return "You got §6$amount §e$";
                }
                if ($i > 0) {
                    $amount = mt_rand(1000, 5000);
                    Functions::safeXPAdd($user, $amount);
                    return "You got §6$amount §eXP!";
                }
                break;
            case "§r§b§lGodly Relic\n§r§fPlace it to claim it":
                $i = mt_rand(1, 100);
                if ($i > 90) {
                    //                    $this->pl->getGoalManager()->add($player, 34);
                    $player->getInventory()->addItem($this->pl->getCEBook('vaulted'));
                    return "You got a §cVaulted §eBook";
                }
                if ($i > 50) {
                    $player->getInventory()->addItem($this->pl->getCEBook('exclusive'));
                    return "You got an §bExclusive §eBook";
                }
                if ($i > 0) {
                    $user->addMana($mana = mt_rand(5000, 50000));
                    return "You get §6$mana §emana";
                }
                break;
            default:
                break;
        }
        return "";
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function getCapeData(string $path) : string {
        $image = imagecreatefrompng($path); // with .png
        $data = '';
        for ($y = 0, $height = imagesy($image); $y < $height; $y++) {
            for ($x = 0, $width = imagesx($image); $x < $width; $x++) {
                $color = imagecolorat($image, $x, $y);
                $data .= pack("c", ($color >> 16) & 0xFF)
                    . pack("c", ($color >> 8) & 0xFF)
                    . pack("c", $color & 0xFF)
                    . pack("c", 255 - (($color & 0x7F000000) >> 23));
            }
        }
        imagedestroy($image);
        return $data;
    }

    /**
     * @param string $name
     *
     * @return int|null
     */
    public function getSellMoneyData(string $name) : ?int {
        $namespace = "minecraft:" . strtolower(str_replace(" ", "_", $name));
        $price = 0;
        if ($this->pl->cfg->exists($namespace)) {
            $price = $this->pl->cfg->get($namespace, 0);
        } elseif ($this->pl->cfg->exists($name)) {
            $price = $this->pl->cfg->get($name, 0);
        }
        return (int) floor($price);
    }

    public function mcmmoSellBoost(User $user, Item $item) : int {
        $farm = [
            ItemTypeIds::CARROT,
            ItemTypeIds::POTATO,
            VanillaBlocks::CACTUS()->asItem()->getTypeId(),
            VanillaBlocks::SUGARCANE()->asItem()->getTypeId(),
            ItemTypeIds::WHEAT,
            VanillaBlocks::MELON()->asItem()->getTypeId(),
            ItemTypeIds::MELON,
            VanillaBlocks::PUMPKIN()->asItem()->getTypeId(),
            ItemTypeIds::BREAD,
            ItemTypeIds::BEETROOT,
            VanillaBlocks::NETHER_WART()->asItem()->getTypeId()
        ];
        $mining = [
            ItemTypeIds::IRON_INGOT,
            ItemTypeIds::COPPER_INGOT,
            ItemTypeIds::LAPIS_LAZULI,
            ItemTypeIds::NETHER_QUARTZ,
            ItemTypeIds::GOLD_INGOT,
            ItemTypeIds::DIAMOND,
            ItemTypeIds::COAL,
            ItemTypeIds::DYE,
            ItemTypeIds::EMERALD,
            ItemTypeIds::REDSTONE_DUST,
            VanillaBlocks::EMERALD()->asItem()->getTypeId(),
            VanillaBlocks::DIAMOND()->asItem()->getTypeId(),
            VanillaBlocks::REDSTONE()->asItem()->getTypeId(),
            VanillaBlocks::IRON()->asItem()->getTypeId(),
            VanillaBlocks::COAL()->asItem()->getTypeId(),
            VanillaBlocks::GOLD()->asItem()->getTypeId(),
            VanillaBlocks::LAPIS_LAZULI()->asItem()->getTypeId(),
            VanillaBlocks::QUARTZ()->asItem()->getTypeId(),
            ItemTypeIds::NETHERITE_SCRAP,
            ItemTypeIds::NETHERITE_INGOT,
            VanillaBlocks::NETHERITE()->asItem()->getTypeId()
        ];
        if (!in_array($item->getTypeId(), array_merge($farm, $mining), true)) return 0;
        if ($item->getTypeId() === ItemTypeIds::DYE && $item->getStateId() !== 4) return 0;
        $level = 0;
        if (in_array($item->getTypeId(), $farm, true)) {
            $level = $user->getLevel("farming");
        } elseif (in_array($item->getTypeId(), $mining, true)) {
            $level = $user->getLevel("mining");
        }
        return (int) (floor($level / 30) * 3);
    }

    /**
     * @param User   $user
     * @param Item   $item
     * @param string $reward
     * @param string $send
     *
     * @return array|null
     */
    public function sellItem(User $user, Item $item, string $reward = 'money', string $send = 'message') : ?array {
        if ($item->getTypeId() < 30000) {
            if ($item->hasCustomName() || $item->hasEnchantments()) {
                return null;
            }
        }
        $user->getPlayer()->sendMessage($item->getTypeId());
        $cfg = Data::$sellPrices[$item->getTypeId()] ?? $this->getSellMoneyData(str_replace(" ", "_", $item->getVanillaName()));
        if ($cfg === null || $cfg === 0) {
            return null;
        }
        $mcboost = $this->mcmmoSellBoost($user, $item);
        if ($reward == 'money') {
            $per = $cfg;
            $price = $per * $item->getCount();
            $price = $this->getTotalAmount($price, $mcboost);
            $user->addMoney($price);
        } else {
            $per = ceil($cfg / 10);
            $price = $per * $item->getCount();
            $price = $this->getTotalAmount($price, $mcboost);
            Functions::safeXPAdd($user, $price);
        }
        //if ($send == 'message') $this->sendMessage($player, "§eSold for §6" . $price . "$type §7(x§c" . $item->getCount() . "§7) §a" . $item->getName() . " §eat §6" . $per . "$type §eeach.");
        $type = ($reward === 'money') ? '$' : 'XP';
        $extra = ($mcboost > 0) ? ' ' . $mcboost . '%' : '';
        if ($send === 'action') $user->getPlayer()->sendActionBarMessage("§l§6>> §eAuto Sold for §6" . $price . "{$type}§a{$extra} §6<<§r");
        return [$per, $mcboost];
    }

    public function uploadChest(Player $player, BaseInventory $inv, Item $item) : void {
        if (($user = $this->pl->getUserManager()->getOnlineUser($player->getName())) === null) return;
        if ($user->getPref()->exclcmdmessages) {
            $this->sendMessage($player, "§eSearching for vanilla §a{$item->getName()} §ein Chest inventory...");
        }
        $count = 0;
        foreach ($inv->getContents() as $i) {
            if ($i->getTypeId() === $item->getTypeId() and $i->getStateId() === $item->getTypeId()) {
                if (!$i->hasEnchantments() and !$i->hasCustomName() and count($i->getLore()) < 2) {
                    $count += $i->getCount();
                    $inv->remove($i);
                }
            }
        }
        $cloud = $this->pl->clouds[strtolower($player->getName())];
        $item->setCount($count);
        $cloud->addItem($item->getVanillaName(), $item->getCount(), false);
        $name = $item->getName();
        if ($count === 0) {
            $this->sendMessage($player, "§4[Error]§c Didn't find any vanilla {$name} in this chest!");
        } else {
            if ($user->getPref()->exclcmdmessages) {
                $this->sendMessage($player, "§eUploaded Item to your ItemCloud account.\n§7Uploaded §7x§c{$item->getCount()} §7of §a{$name} §7from the Chest");
            }
        }
    }

    public function downloadChest(Player $player, BaseInventory $inv, Item $item) : void {
        if (($user = $this->pl->getUserManager()->getOnlineUser($player->getName())) === null) return;
        $cloud = $this->pl->clouds[strtolower($player->getName())];
        $count = $cloud->getCount($item->getVanillaName());
        if ($count <= 0) {
            $this->sendMessage($player, "§4[Error]§c You dont have any §a{$item->getName()} §cin your ItemCloud to download!");
            return;
        }
        if ($user->getPref()->exclcmdmessages) {
            $this->sendMessage($player, "§eDownloading §7x§c{$count} §a{$item->getName()} §eto Chest inventory...");
        }
        if (($slots = Util::getSlotsForItem($inv, $item)) <= 0) {
            $this->sendMessage($player, "§4[Error] §cChest inventory is too full to add that item!");
            return;
        }
        if ($count > $slots) $count = $slots;
        $item->setCount($count);
        $cloud->removeItem($item->getTypeId(), $item->getCount());
        $inv->addItem($item);
        if ($user->getPref()->exclcmdmessages) {
            $this->sendMessage($player, "§eDownloaded Item to Chest from your ItemCloud account..\n§7Downloaded - x§c{$count} §a{$item->getName()}");
        }
    }

    /**
     * @param BaseInventory $inv
     * @param User          $user
     *
     * @return bool
     */
    public function condenseChest(BaseInventory $inv, User $user) : bool {
        $player = $user->getPlayer();
        $condenseShapes = [
            VanillaItems::COAL()->getVanillaName()          => VanillaBlocks::COAL(),
            VanillaItems::IRON_INGOT()->getVanillaName()    => VanillaBlocks::IRON(),
            VanillaItems::GOLD_INGOT()->getVanillaName()    => VanillaBlocks::GOLD(),
            VanillaItems::DIAMOND()->getVanillaName()       => VanillaBlocks::DIAMOND(),
            VanillaItems::EMERALD()->getVanillaName()       => VanillaBlocks::EMERALD(),
            VanillaItems::LAPIS_LAZULI()->getVanillaName()  => VanillaBlocks::LAPIS_LAZULI(),
            VanillaItems::NETHER_QUARTZ()->getVanillaName() => VanillaBlocks::QUARTZ(),
        ];
        $this->sendMessage($player, "§eSearching all Items in inventory...");
        $totcount = [];
        $flag = false;
        foreach ($inv->getContents() as $item) {
            $str = $item->getVanillaName();
            if (!isset($condenseShapes[$str])) continue;
            if (!isset($totcount[$str])) $totcount[$str]["count"] = 0;
            $totcount[$str]["count"] += $item->getCount();
            $totcount[$str]["namespace"] = $item->getVanillaName();
        }
        foreach ($totcount as $namespace => $data) {
            $icount = $data["count"];
            if ($data["namespace"] === BlockTypeIds::QUARTZ) $shape = 4;
            else $shape = 9;
            $totbl = (int) floor($icount / $shape);
            $left = (int) $icount % $shape;
            if ($totbl < 1) continue;
            $newItems = $condenseShapes[$namespace]->asItem()->setCount($totbl);
            $leftItems = StringToItemParser::getInstance()->parse($data["namespace"])->setCount($left);
            $temp = StringToItemParser::getInstance()->parse($data['namespace'])->setCount($icount);
            $inv->remove($temp);
            $canadd = true;
            if (!$inv->canAddItem($newItems)) $canadd = false;
            if (!$inv->canAddItem($leftItems)) $canadd = false;
            if (!$canadd) {
                $inv->addItem($temp);
            } else {
                $flag = true;
                $inv->addItem($newItems);
                $inv->addItem($leftItems);
            }
        }
        return $flag;
    }

    /**
     * @param BaseInventory $inv
     * @param User          $user
     * @param string        $type
     */
    public function sellChest(BaseInventory $inv, User $user, string $type) : void {
        $items = $inv->getContents();
        $sold = [];
        foreach ($items as $item) {
            if (($data = $this->sellItem($user, $item, $type)) !== null) {
                $key = $item->getTypeId() . ":" . $item->getName();
                if (isset($sold[$key])) $sold[$key]["count"] += $item->getCount();
                else {
                    $sold[$key] = ["count" => $item->getCount(), "per" => $data[0], "boost" => $data[1]];
                    $sold[$key]["name"] = $this->getSellItemName($item);
                }
                $inv->remove($item);
            }
        }
        if ($user->getPref()->exclcmdmessages) {
            $user->getPlayer()->sendMessage($this->getSellString($sold, $type));
        }
    }

    /**
     * @param Item $item
     *
     * @return string
     */
    public function getSellItemName(Item $item) : string {
        return $item->getName();
    }

    /**
     * @param array  $sold
     * @param string $type
     *
     * @return string
     */
    public function getSellString(array $sold, string $type) : string {
        $str = "§l§e[Sold]>§r ";
        $cost = 0;
        $symbol = ($type === "money") ? "$" : "xp";
        if (empty($sold)) $str = "§e§lFound no sellable items!";
        else {
            foreach ($sold as $data) {
                $money = $data['per'] * $data['count'];
                $extra = '';
                if ($data['boost'] > 0) {
                    $money = $this->getTotalAmount($money, $data['boost']);
                    $extra = '§d+' . $data['boost'] . '%';
                }
                $cost += $money;
                $str .= "§7x§c{$data['count']} §a{$data['name']}§7(§6" . $data['per'] . $symbol . "{$extra}§7), ";
            }
            $str = substr($str, 0, -2);
            $str .= ". §bReceived §f= §6" . number_format($cost) . $symbol;
        }
        return $str;
    }

    /**
     * @param int $og
     * @param int $boost
     *
     * @return int
     */
    public function getTotalAmount(int $og, int $boost) : int {
        $cur = ($boost / 100) * $og;
        return (int) ($og + $cur);
    }

    public function getRandomRelic(bool $prosperity = false) : ?Item {
        $chance = mt_rand(1, 1000);
        $item = null;
        if ($prosperity) {
            if ($chance > 950) {
                $item = Main::getInstance()->getRelic("godly");
            } else if ($chance > 850) {
                $item = Main::getInstance()->getRelic("mythic");
            } elseif ($chance > 550) {
                $item = Main::getInstance()->getRelic("legendary");
            } elseif ($chance > 100) {
                $item = Main::getInstance()->getRelic("rare");
            } elseif ($chance > 0) {
                $item = Main::getInstance()->getRelic();
            }
        } else {
            if ($chance <= 500 and $chance > 0) {
                $item = $this->pl->getRelic();
            } elseif ($chance > 500 and $chance <= 750) {
                $item = $this->pl->getRelic('rare');
            } elseif ($chance > 750 and $chance <= 900) {
                $item = $this->pl->getRelic('legendary');
            } elseif ($chance > 900 and $chance <= 975) {
                $item = $this->pl->getRelic('mythic');
            } elseif ($chance > 975) {
                $item = $this->pl->getRelic('godly');
            }
        }

        return $item;
    }

    public function loadPlayerPet(Player $p) : void {
        $user = $this->pl->getUserManager()->getOnlineUser($p->getName());
        if ($user->hasSetPet()) {
            $pet = $this->pl->createPet($user->getSelectedPet(), $p, $user->getPetName());
            if (!is_null($pet)) {
                $pet->spawnToAll();
                $pet->setDormant(false);
            }
        }
    }

    public function checkCustomItem(Player $player, Item $item, Block $block) : bool {
        if ($item->hasCustomName()) {
            if (!isset($this->pl->using[strtolower($player->getName())]) || $this->pl->using[strtolower($player->getName())] <= time()) {
                if (count(explode("/n", $item->getCustomName())) > 0) {
                    $words = preg_split("/[\s,_-]+/", $item->getCustomName());
                    switch ($item->getTypeId()) {
                        case VanillaItems::BOOK()->getTypeId();
                        case CustomItems::VOTE_KEY()->getTypeId():
                        case CustomItems::COMMON_KEY()->getTypeId():
                        case CustomItems::RARE_KEY()->getTypeId():
                        case CustomItems::LEGENDARY_KEY()->getTypeId():
                        case CustomItems::MYSTIC_KEY()->getTypeId():
                        case CustomItems::CE_KEY()->getTypeId():
                        case CustomItems::VE_KEY()->getTypeId():
                            $this->checkCustomName($words, $item, $block, $player);
                            return true;
                        case VanillaItems::ENCHANTED_BOOK()->getTypeId():
                            $this->checkCEBook($player, strtolower(TF::clean($words[1])));
                            return true;
                        case VanillaItems::NAME_TAG()->getTypeId():
                            $this->pl->using[strtolower($player->getName())] = time() + 1;
                            $this->checkTagName($words, $item, $player);
                            return true;
                        case VanillaItems::POPPED_CHORUS_FRUIT()->getTypeId():
                            $this->pl->using[strtolower($player->getName())] = time() + 1;
                            $this->checkKitEffects($words, $item, $player);
                            return true;
                        case VanillaItems::PAPER()->getTypeId():
                            $this->pl->using[strtolower($player->getName())] = time() + 1;
                            $this->checkCheque($words, $item, $player);
                            return true;
                        case CustomItems::CARROT_ON_A_STICK()->getTypeId():
                            $this->pl->using[strtolower($player->getName())] = time() + 1;
                            $this->checkKothWinner($words, $player, $item->getCustomName());
                            return true;
                        case VanillaItems::NAUTILUS_SHELL()->getTypeId():
                            $this->pl->using[strtolower($player->getName())] = time() + 1;
                            $this->checkXP($words, $item, $player);
                            return true;
                        case VanillaItems::HEART_OF_THE_SEA()->getTypeId():
                            $this->pl->using[strtolower($player->getName())] = time() + 1;
                            $this->checkKitChips($words, $item, $player);
                            return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param Player $player
     * @param Item   $item
     *
     * @return bool
     */
    public function checkIfRelic(Player $player, Item $item) : bool {
        if ($item->hasCustomName() && in_array($item->getCustomName(), array_column($this->pl->relics, "name"), true)) {
            if ($this->pl->getFunctions()->isInventoryFull($player)) {
                $this->sendMessage($player, "§cYour Inventory is full. Empty a slot from your inventory!");
                return true;
            }
            $got = $this->checkRelic($item->getName(), $player);
            $this->sendMessage($player, "§aRelic Redeemed! §e$got");
            $item->pop();
            $player->getInventory()->setItemInHand($item);
            return true;
        }
        return false;
    }


    /**
     * @param SBPlayer  $player
     * @param Container $tile
     *
     * @return bool
     */
    public function addChestItems(Player $player, Container $tile) : bool {
        $inventory = $tile->getInventory();
        $contents = array_merge($inventory->getContents(), [VanillaBlocks::CHEST()->asItem()]);
        foreach ($contents as $content) {
            if ($player->getInventory()->canAddItem($content)) {
                $player->getInventory()->addItem($content);
                $inventory->removeItem($content);
            } else    return false;
        }
        return true;
    }

    /**
     * @param SBPlayer $player
     * @param          $drop
     * @param bool     $barter
     * @param bool     $tinkerer
     *
     * @return bool
     */
    public function addItemInInventory(Player $player, $drop, bool $barter, bool $tinkerer) : bool {
        if ($barter and $tinkerer) $tinkerer = false;
        if ($drop instanceof Item) {
            $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
            if ($player->getInventory()->canAddItem($drop)) {
                if ($barter) {
                    if ($this->sellItem($user, $drop, 'money', 'action') !== null) return true;
                }
                if ($tinkerer) {
                    if ($this->sellItem($user, $drop, 'xp', 'action') !== null) return true;
                }
                $player->getInventory()->addItem($drop);
                return true;
            } else {
                if (!isset($this->pl->inv_full[$player->getName()]) or time() > $this->pl->inv_full[$player->getName()]) {
                    $this->pl->inv_full[$player->getName()] = time() + 5;
                    $this->sendMessage($player, TextFormat::RED . "Your inventory is full!");
                }
                return false;
            }
        }
        return true;
    }

    private function whatTier(int $typeId) : array {
        if (in_array($typeId, Data::$stoneOres, true)) {
            return Data::$stoneOres;
        } elseif (in_array($typeId, Data::$deepslateOres, true)) {
            return Data::$deepslateOres;
        } elseif (in_array($typeId, Data::$netherrackOres, true)) {
            return Data::$netherrackOres;
        } elseif (in_array($typeId, Data::$endstoneOres, true)) {
            return Data::$endstoneOres;
        } elseif (in_array($typeId, Data::$blackstoneOres, true)) {
            return Data::$blackstoneOres;
        } elseif (in_array($typeId, Data::$prismarineOres, true)) {
            return Data::$prismarineOres;
        } elseif (in_array($typeId, Data::$amethystOres, true)) {
            return Data::$amethystOres;
        }
        return [];
    }

    public function handleMinesBlockBreakEvent(BlockBreakEvent $event) : void {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if (in_array($block->getTypeId(), Data::$mineableBlocks)) {
            $heldItem = $player->getInventory()->getItemInHand();
            if ($heldItem instanceof Pickaxe || $heldItem instanceof \SkyBlock\item\Pickaxe) {
                $event->getBlock()->getPosition()->getWorld()->setBlock($event->getBlock()->getPosition(), VanillaBlocks::BEDROCK());
                $this->blockBreakRewards($event);
                Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($block, $player) : void {
                    $array = $this->whatTier($block->getTypeId());
                    if ($array !== []) {
                        $name = Data::$typeIdToNameMap[$array[array_rand($array)]];
                        $newBlock = StringToItemParser::getInstance()->parse($name);
                        if ($newBlock instanceof ItemBlock) {
                            $block->getPosition()->getWorld()->setBlock($block->getPosition(), $newBlock->getBlock());
                        }
                    }
                }
                                                                         ), 20 * 5
                );
            } else {
                $event->cancel();
            }
        }
    }

    private function blockBreakRewards(BlockBreakEvent $event) : void {
        $tinkerer = $barter = $lots = $prosperity = false;
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());

        switch ($block->getTypeId()) {
            case VanillaBlocks::COAL_ORE()->getTypeId():
                $event->setDrops([VanillaItems::COAL()->setCount(FortuneDropHelper::weighted($event->getItem(), 1, 1))]);
                break;
            case VanillaBlocks::DEEPSLATE_COAL_ORE()->getTypeId():
                $event->setDrops([VanillaItems::COAL()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 2))]);
                break;
            case VanillaBlocks::COPPER_ORE()->getTypeId():
                $event->setDrops([VanillaItems::RAW_COPPER()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 1))]);
                break;
            case VanillaBlocks::DEEPSLATE_COPPER_ORE()->getTypeId():
                $event->setDrops([VanillaItems::RAW_COPPER()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 2))]);
                break;
            case VanillaBlocks::DIAMOND_ORE()->getTypeId():
                $event->setDrops([VanillaItems::DIAMOND()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 1))]);
                break;
            case VanillaBlocks::DEEPSLATE_DIAMOND_ORE()->getTypeId():
                $event->setDrops([VanillaItems::DIAMOND()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 2))]);
                break;
            case VanillaBlocks::EMERALD_ORE()->getTypeId():
                $event->setDrops([VanillaItems::EMERALD()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 1))]);
                break;
            case VanillaBlocks::DEEPSLATE_EMERALD_ORE()->getTypeId():
                $event->setDrops([VanillaItems::EMERALD()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 2))]);
                break;
            case VanillaBlocks::GOLD_ORE()->getTypeId():
                $event->setDrops([VanillaItems::RAW_GOLD()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 1))]);
                break;
            case VanillaBlocks::DEEPSLATE_GOLD_ORE()->getTypeId():
                $event->setDrops([VanillaItems::RAW_GOLD()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 2))]);
                break;
            case VanillaBlocks::IRON_ORE()->getTypeId():
                $event->setDrops([VanillaItems::RAW_IRON()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 1))]);
                break;
            case VanillaBlocks::DEEPSLATE_IRON_ORE()->getTypeId():
                $event->setDrops([VanillaItems::RAW_IRON()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 2))]);
                break;
            case VanillaBlocks::LAPIS_LAZULI_ORE()->getTypeId():
                $event->setDrops([VanillaItems::LAPIS_LAZULI()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 1))]);
                break;
            case VanillaBlocks::DEEPSLATE_LAPIS_LAZULI_ORE()->getTypeId():
                $event->setDrops([VanillaItems::LAPIS_LAZULI()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 2))]);
                break;
            case VanillaBlocks::NETHER_QUARTZ_ORE()->getTypeId():
                $event->setDrops([VanillaItems::NETHER_QUARTZ()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 2))]);
                break;
            case VanillaBlocks::REDSTONE_ORE()->getTypeId():
                $event->setDrops([VanillaItems::REDSTONE_DUST()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 1))]);
                break;
            case VanillaBlocks::DEEPSLATE_REDSTONE_ORE()->getTypeId():
                $event->setDrops([VanillaItems::REDSTONE_DUST()->setCount(FortuneDropHelper::discrete($event->getItem(), 1, 2))]);
                break;

        }

        if ($player->getInventory()->getItemInHand()->hasEnchantments()) {
            foreach ($player->getInventory()->getItemInHand()->getEnchantments() as $enchantment) {
                $type = $enchantment->getType();
                if ($type instanceof BaseBlockBreakEnchant && $type->isApplicableTo($player, $enchantment->getLevel())) {
                    if ($type instanceof Barter) {
                        $barter = true;
                        $user->addMoney(Functions::calcTinkBarterMoneyXp($enchantment->getLevel()));
                    }
                    if ($type instanceof Tinkerer) {
                        $tinkerer = true;
                        Functions::safeXPAdd($user, Functions::calcTinkBarterMoneyXp($enchantment->getLevel()));
                    }
                    if ($type instanceof LuckOfTheSky) {
                        $lots = $enchantment->getLevel();
                    }
                    if ($type instanceof Prosperity) {
                        $prosperity = true;
                    }
                    if ($type instanceof Insurance) {
                        $hand = $player->getInventory()->getItemInHand();
                        if ($hand instanceof Durable) {
                            if ($hand->getDamage() <= 5) {
                                $type->onActivation($player, $event, $enchantment->getLevel());
                                $event->cancel();
                                return;
                            }
                        }
                    }
                    $type->onActivation($player, $event, $enchantment->getLevel());
                }
            }
        }

        $user->addBlocksBroken();
        Main::getInstance()->serverblocks++;
        $player->getHungerManager()->exhaust(0.25, PlayerExhaustEvent::CAUSE_MINING);

        $maxRandom = 1500;
        if (is_int((int) $lots)) {
            $maxRandom -= (int) $lots * ($lots <= 10 ? 40 : 55);
        }
        if (mt_rand(1, $maxRandom) < 5) {
            $item = Main::getInstance()->getEvFunctions()->getRandomRelic($prosperity);
            $player->sendMessage("§b§l>> §eYou got a §aRelic! §b<<§r");
            $drops = $event->getDrops();
            $drops[] = $item;
            $event->setDrops($drops);
        }
        $user->addMana(Data::$blockManaValues[$block->getTypeId()]);
        Functions::safeXPAdd($user, Data::$blockXpValues[$block->getTypeId()]);
        if ($user->isIslandSet()) {
            Main::getInstance()->getIslandManager()->getOnlineIsland($user->getIsland())->setPoints(Data::$blockIslandPointValues[$block->getTypeId()]);
        }
        $player->getInventory()->setItemInHand(ItemManager::getInstance()->doItemTasks($player->getInventory()->getItemInHand(), $event));

        foreach ($event->getDrops() as $drop) {
            if (is_array($drop)) {
                foreach ($drop as $d) {
                    if (!$this->addItemInInventory($player, $d, $barter, $tinkerer)) {
                        break;
                    }
                }
            } else {
                $this->addItemInInventory($player, $drop, $barter, $tinkerer);
            }
        }
    }
}