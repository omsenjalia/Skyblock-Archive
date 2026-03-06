<?php

namespace SkyBlock\command;

use pocketmine\block\BlockTypeIds;
use pocketmine\block\tile\Container;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\block\upgrade\LegacyBlockIdToStringIdMap;
use pocketmine\entity\{Entity,
    Human,
    object\ExperienceOrb,
    object\FallingBlock,
    object\ItemEntity,
    object\Painting,
    object\PrimedTNT,
    projectile\Arrow,
    projectile\Egg,
    projectile\EnderPearl,
    projectile\Snowball,
    projectile\SplashPotion};
use pocketmine\item\Armor;
use pocketmine\item\Axe;
use pocketmine\item\Bow;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Hoe;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\Pickaxe;
use pocketmine\item\Shovel;
use pocketmine\item\StringToItemParser;
use pocketmine\item\Sword;
use pocketmine\item\Tool;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\utils\Limits;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use pocketmine\world\World;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\Main;
use SkyBlock\pets\BasePet;
use SkyBlock\spawner\Creature;
use SkyBlock\user\User;
use SkyBlock\util\Lore;
use SkyBlock\util\Util;
use SkyBlock\util\Values;

class Functions {

    /** @var Main */
    private Main $pl;

    /**
     * Functions constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->pl = $plugin;
    }

    /**
     * @param Player $sender
     * @param        $message
     */
    public function sendMessage(Player $sender, $message) : void {
        $sender->sendMessage(Values::FT_PREFIX . $message);
    }

    /**
     * @param Player $player
     */
    public function sendAuctionHelp(Player $player) : void {
        $str = str_repeat(TF::GOLD . '+' . TF::GREEN . '-', 7) . TF::GOLD . "+\n§r";
        $helps = [
            '/ah list <page>'              => 'List all auctions hosted',
            '/ah filter <filter>'          => 'List all auctiond with filters applied',
            '/ah user <sellername> <page>' => 'List all auctions hosted by seller',
            '/ah brag <auctionID>'         => 'Brag the item on our discord channel',
            '/ah buy <auctionID>'          => 'Buy an item off auction',
            '/ah sell <price>'             => 'Put your item in hand on auction.',
            '/ah takeoff <auctionID>'      => 'Take your item off auction',
            '/ah info <auctionID>'         => 'Get detailed information of an item.'
        ];
        foreach ($helps as $cmd => $desc) {
            $str .= TF::AQUA . $cmd . ' ' . TF::WHITE . $desc . "\n§r";
        }
        $str .= str_repeat(TF::GOLD . '+' . TF::GREEN . '-', 7) . TF::GOLD . "+\n§r";
        $this->sendMessage($player, $str);
    }

    /**
     * @return int
     */
    public function getFreeKey() : int {
        $i = 0;
        while (isset($this->pl->auctions[$i])) $i++;
        return $i;
    }

    /**
     * @param Player $player
     * @param array  $members
     * @param array  $kills
     * @param array  $deaths
     */
    public function sendMembersList(Player $player, array $members, array $kills, array $deaths) : void {
        $on = '§a[ON]';
        $off = '§c[OFF]';
        $i = 1;
        $str = "";
        foreach ($members as $member) {
            if ($this->pl->getUserManager()->getOnlineUser($member) !== null) {
                $str .= "§f{$i}. §f{$member} {$on} §7[§6K: {$kills[$member]} §4D: {$deaths[$member]}§7]\n";
            } else {
                $str .= "§f{$i}. §f{$member} {$off} §7[§6K: {$kills[$member]} §4D: {$deaths[$member]}§7]\n";
            }
            ++$i;
        }
        $player->sendMessage($str);
    }

    /**
     * @param Item   $it
     * @param string $type
     *
     * @return int
     */
    public function countEnchants(Item $it, string $type = "vanilla") : int {
        $i = 0;
        foreach ($it->getEnchantments() as $e) {
            if ($type == "vanilla") {
                if (BaseEnchantment::getEnchantmentId($e) <= 22) $i++;
            }
            if ($type == "ce") {
                if (BaseEnchantment::getEnchantmentId($e) >= 100) $i++;
            }
        }
        return $i;
    }

    /**
     * @param int $accuracy
     *
     * @return int
     */
    public function getAccuracyValue(int $accuracy) : int {
        if ($accuracy >= 97) {
            return 2;
        }
        if ($accuracy >= 80) {
            $i = mt_rand(1, 6);
            if ($i == 1) {
                return 1;
            } else return 2;
        }
        if ($accuracy >= 50 && $accuracy < 80) {
            $i = mt_rand(1, 6);
            if ($i == 1 || $i == 2 || $i == 3) {
                return 1;
            } else return 2;
        }
        if ($accuracy >= 25 && $accuracy < 50) {
            $i = mt_rand(1, 6);
            if ($i == 1 || $i == 2 || $i == 3 || $i == 4) {
                return 0;
            } else return 2;
        }
        if ($accuracy >= 1 && $accuracy < 25) {
            $i = mt_rand(1, 6);
            if ($i == 1) {
                return 2;
            } else return 0;
        }
        return 2;
    }

    /**
     * @param Item $item
     *
     * @return string|null
     */
    public static function getItemtype(Item $item) : ?string {
        if ($item instanceof Sword) {
            return "Sword";
        } elseif ($item instanceof Bow) {
            return "Bow";
        } elseif ($item instanceof Axe or $item instanceof \SkyBlock\item\Axe) {
            return "Axe";
        } elseif ($item instanceof Pickaxe or $item instanceof \SkyBlock\item\Pickaxe) {
            return "Pickaxe";
        } elseif ($item instanceof Hoe) {
            return "Hoe";
        } elseif ($item instanceof Shovel or $item instanceof \SkyBlock\item\Shovel) {
            return "Shovel";
        } elseif ($item instanceof Armor) {
            $helmets = [ItemTypeIds::DIAMOND_HELMET, ItemTypeIds::CHAINMAIL_HELMET, ItemTypeIds::IRON_HELMET, ItemTypeIds::GOLDEN_HELMET, ItemTypeIds::LEATHER_CAP, ItemTypeIds::TURTLE_HELMET];
            $chest = [ItemTypeIds::DIAMOND_CHESTPLATE, ItemTypeIds::CHAINMAIL_CHESTPLATE, ItemTypeIds::IRON_CHESTPLATE, ItemTypeIds::GOLDEN_CHESTPLATE, ItemTypeIds::LEATHER_TUNIC];
            $leg = [ItemTypeIds::DIAMOND_LEGGINGS, ItemTypeIds::CHAINMAIL_LEGGINGS, ItemTypeIds::IRON_LEGGINGS, ItemTypeIds::GOLDEN_LEGGINGS, ItemTypeIds::LEATHER_PANTS];
            $boots = [ItemTypeIds::DIAMOND_BOOTS, ItemTypeIds::CHAINMAIL_BOOTS, ItemTypeIds::IRON_BOOTS, ItemTypeIds::GOLDEN_BOOTS, ItemTypeIds::LEATHER_BOOTS];
            if (in_array($item->getTypeId(), $helmets, true)) {
                return "Helmet";
            } elseif (in_array($item->getTypeId(), $chest, true)) {
                return "Chestplate";
            } elseif (in_array($item->getTypeId(), $leg, true)) {
                return "Leggings";
            } elseif (in_array($item->getTypeId(), $boots, true)) {
                return "Boots";
            }
        }
        return null;
    }

    /**
     * @param string $cetype
     * @param string $itemtype
     *
     * @return bool
     */
    public function checkCompatibility(string $cetype, string $itemtype) : bool {
        $cetype = strtolower($cetype);
        $itemtype = strtolower($itemtype);
        if ($cetype === $itemtype) return true;
        if ($cetype === 'tool') {
            if ($itemtype === 'pickaxe' || $itemtype === 'axe') return true;
        }
        if ($cetype === 'armor') {
            if ($itemtype === 'helmet' || $itemtype === 'chestplate' || $itemtype === 'leggings' || $itemtype === 'boots') return true;
        }
        return false;
    }

    /**
     * @param string $cename
     *
     * @return int|null
     */
    public function getEnchantmentId(string $cename) : ?int {
        $ce = $this->pl->getEnchantFactory()->getIdByEnchantName(TF::clean($cename));
        return $ce;
    }

    /**
     * @param Player $player
     *
     * @return int
     */
    public function getUserHelperLimit(Player $player) : int {
        $staff = 3;
        if ($this->pl->staffapi->hasStaffRank($player->getName())) $staff = 10;
        $limit = match ($this->rank($player->getName())) {
            'King' => 4,
            'VIP' => 5,
            'Myth' => 7,
            'SkyLord' => 10,
            'SkyGOD' => 13,
            'SkyZEUS' => 15,
            'SkyELITE' => 20,
            'SkyHULK' => 25,
            'SkyWARRIOR' => 30,
            default => 3
        };
        return max($staff, $limit);
    }

    /**
     * @param string $player
     *
     * @return string
     */
    public function rank(string $player) : string {
        return $this->pl->permsapi->getUserGroup($player)->getName();
    }

    public function sendFilteredAuctionList(Player $player, string $itemid, int $enchid, string $order, string $sort, int $page) : bool {
        $i = 0;
        $startnum = $page * 11;
        $endnum = 0;
        if ($page == 1) {
            $endnum = $startnum - 11;
        }
        if ($page > 1) {
            $endnum = $startnum - 10;
        }
        $auctions = $this->pl->auctions;
        if ($order == 'id' and $sort == 'asc') ksort($auctions);
        if ($order == 'id' and $sort == 'desc') krsort($auctions);
        if ($order == 'price' and $sort == 'asc') {
            uasort($auctions, function($a, $b) {
                if ($a["price"] == $b["price"]) return 0;
                return ($a["price"] < $b["price"]) ? -1 : 1;
            }
            );
        }
        if ($order == 'price' and $sort == 'desc') {
            uasort($auctions, function($a, $b) {
                if ($a["price"] == $b["price"]) return 0;
                return ($a["price"] < $b["price"]) ? 1 : -1;
            }
            );
        }
        $str = TF::GREEN . "FILTERED AUCTION LIST:\n";
        $str .= TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 10) . TF::AQUA . '[ Page : ' . $page . ' ]' . TF::WHITE . str_repeat('=', 10) . TF::YELLOW . "[+]\n";
        foreach ($auctions as $id => $data) {
            if ($itemid != $data['name']) continue;
            if (!$this->hasEnchantIdAuction($enchid, $data)) continue;
            ++$i;
            if ($i >= $endnum and $i <= $startnum) {
                $str .= TF::AQUA . 'ID-' . $id . ' => ' . TF::YELLOW . $data['name'] . TF::GRAY . '(' . TF::GREEN . 'x' . $data['count'] . TF::GRAY . ') for ' . TF::GOLD . '$' . number_format($data['price']) . TF::GRAY . ' by ' . TF::GREEN . $data['seller'] . "\n";
            }
        }
        $this->sendMessage($player, $str . TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 26) . TF::YELLOW . '[+]');
        return true;
    }

    /**
     * @param int   $eid
     * @param array $data
     *
     * @return bool
     */
    public function hasEnchantIdAuction(int $eid, array $data) : bool {
        $item = Item::nbtDeserialize($data['item']);
        if ($item->hasEnchantments()) {
            foreach ($item->getEnchantments() as $dat) {
                if (BaseEnchantment::getEnchantmentId($dat) === $eid) return true;
            }
        }
        return false;
    }

    /**
     * @param Player $player
     * @param int    $page
     *
     * @return bool
     */
    public function sendAuctionList(Player $player, int $page) : bool {
        $i = 0;
        $total = count($this->pl->auctions);
        $pages = ceil($total / 11);
        if ($pages < $page) {
            $this->sendMessage($player, "§4[Error]§c That page cannot be found.\n" . TF::AQUA . "Last page = " . TF::GREEN . "$pages");
            return true;
        }
        $startnum = $page * 11;
        $endnum = 0;
        if ($page == 1) {
            $endnum = $startnum - 11;
        }
        if ($page > 1) {
            $endnum = $startnum - 10;
        }
        krsort($this->pl->auctions);
        $bb = TF::GREEN . "GLOBAL AUCTION LIST:\n" . TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 10) . TF::AQUA . '[ ' . $page . '/' . $pages . ' ]' . TF::WHITE . str_repeat('=', 10) . TF::YELLOW . "[+]\n";
        foreach ($this->pl->auctions as $id => $data) {
            ++$i;
            if ($i >= $endnum and $i <= $startnum) {
                $bb .= TF::AQUA . 'ID-' . $id . ' => ' . TF::YELLOW . $data['name'] . TF::GRAY . '(' . TF::GREEN . 'x' . $data['count'] . TF::GRAY . ') for ' . TF::GOLD . '$' . number_format($data['price']) . TF::GRAY . ' by ' . TF::GREEN . $data['seller'] . "\n";
            }
        }
        $player->sendMessage($bb . TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 26) . TF::YELLOW . '[+]');
        return true;
    }

    /**
     * @param Player $play
     * @param string $name
     * @param int    $page
     *
     * @return bool
     */
    public function sendUserAuctions(Player $play, string $name, int $page) : bool {
        $x = 0;
        $total = $this->userSellCount($name);
        if ($total == 0) {
            $this->sendMessage($play, '§4[Error]§c ' . $name . ' is not hosting any auctions.');
            return true;
        }
        $pages = ceil($total / 5);
        if ($pages < $page) {
            $this->sendMessage($play, "§4[Error]§c That page cannot be found.\n" . TF::AQUA . "Last page = " . TF::GREEN . "$pages");
            return true;
        }
        $startnum = $page * 5;
        $endnum = 0;
        if ($page == 1) {
            $endnum = $startnum - 5;
        }
        if ($page > 1) {
            $endnum = $startnum - 4;
        }
        if ($total > 0) {
            krsort($this->pl->auctions);
            $this->sendMessage($play, TF::GREEN . "$name" . "'s AUCTION LIST:");
            $bb = TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 10) . TF::AQUA . '[ ' . $page . '/' . $pages . ' ]' . TF::WHITE . str_repeat('=', 10) . TF::YELLOW . "[+]\n";
            foreach ($this->pl->auctions as $id => $data) {
                if ($data['seller'] == $name) {
                    ++$x;
                    if ($x >= $endnum and $x <= $startnum) {
                        $bb .= TF::AQUA . 'ID-' . $id . ' => ' . TF::YELLOW . $data['name'] . TF::GRAY . '(' . TF::GREEN . 'x' . $data['count'] . TF::GRAY . ') for ' . TF::GOLD . '$' . number_format($data['price']) . TF::GRAY . ' by ' . TF::GREEN . $data['seller'] . "\n";
                    }
                }
            }
            $play->sendMessage($bb . TF::YELLOW . '[+]' . TF::WHITE . str_repeat('=', 26) . TF::YELLOW . '[+]');
        }
        return true;
    }

    /**
     * @param $name
     *
     * @return int
     */
    public function userSellCount($name) : int {
        $i = 0;
        foreach ($this->pl->auctions as $data) {
            if ($data['seller'] == $name) {
                $i++;
            }
        }
        return $i;
    }

    /**
     * @param int    $aucId
     * @param Player $player
     * @param string $name
     * @param array  $args
     *
     * @return bool
     */
    public function buyAuction(int $aucId, Player $player, string $name, array $args) : bool {
        if (isset($this->pl->auctions[$aucId])) {
            $auction = $this->pl->auctions[$aucId];
            $itemprice = Util::convertToFloat($auction['price']);
            $user = $auction['seller'];
            if ($user == $name) {
                $this->sendMessage($player, "§4[Error]§c You can't purchase your own Auction.");
                return false;
            }
            if (!isset($this->pl->aucconfirm[$player->getName()])) {
                $this->pl->aucconfirm[$player->getName()] = $aucId;
                $this->sendMessage($player, "§eAre you sure you want to buy this auction, priced §6" . number_format($itemprice) . "§e$, send §a/ah buy $aucId confirm");
                return false;
            } else {
                if (!isset($args[2])) {
                    $this->sendMessage($player, "§ePlease enter confirm at the end to buy the auction, §a/ah buy $aucId confirm");
                    return false;
                }
                if (strtolower($args[2]) == "confirm") {
                    $aid = $this->pl->aucconfirm[$player->getName()];
                    if ($aid != $aucId) {
                        unset($this->pl->aucconfirm[$player->getName()]);
                        $this->sendMessage($player, "§4[Error]§c Auction ID doesnt match with the last id §e$aid, §cplease use §a/ah buy $aid confirm");
                        return false;
                    }
                    $user2 = $this->pl->getUserManager()->getOnlineUser($player->getName());
                    if (!$user2->hasMoney($itemprice)) {
                        unset($this->pl->aucconfirm[$player->getName()]);
                        $this->sendMessage($player, "§4[Error]§c You don't have enough money to buy this item.");
                        return false;
                    } else $user2->removeMoney($itemprice);
                    if (($user3 = $this->pl->getUserManager()->getOnlineUser($user)) === null) {
                        $this->pl->getDb()->addUserMoney($user, $itemprice);
                    } else {
                        $user3->addMoney($itemprice, false);
                        $this->sendMessage($user3->getPlayer(), '§a' . $player->getName() . ' §ehas purchased your item §7(§a' . $auction['name'] . '§7) §efor §6$' . number_format($itemprice));
                    }
                    $player->getInventory()->addItem(Item::nbtDeserialize($auction['item']));
                    $this->sendMessage($player, '§eYou have successfully purchased the item ' . TF::AQUA . "$aucId" . TF::YELLOW . ' off auction for §6$' . number_format($itemprice));
                    unset($this->pl->aucconfirm[$player->getName()]);
                    unset($this->pl->auctions[$aucId]);
                } else {
                    $this->sendMessage($player, "§ePlease enter confirm at the end to buy the auction, §a/ah buy $aucId confirm");
                    return false;
                }
            }
        } else {
            $this->sendMessage($player, '§4[Error]§c The auction with the ID ' . $aucId . ' cannot be found.');
        }
        return true;
    }

    /**
     * @param Item $item
     * @param Item $item2
     *
     * @return bool
     */
    public static function itemEquals(Item $item, Item $item2) : bool {
        return $item->getTypeId() === $item2->getTypeId() and $item->getCount() === $item2->getCount() and
            $item->getName() === $item2->getName() and $item->getCustomName() === $item2->getCustomName() and
            ($item->getStateId() === $item2->getStateId()) and ($item->getLore() === $item2->getLore()) and
            ($item->getNamedTag()->equals($item2->getNamedTag()));
    }

    /**
     * @param User $user
     * @param int  $xp
     *
     * @return void
     */
    public static function safeXPAdd(User $user, int $xp) : void {
        if (($user->getXP() + $xp) > Limits::INT32_MAX) {
            Main::getInstance()->sendMessage($user->getPlayer(), "§cYour XP total is exceeding the limit - " . number_format(Limits::INT32_MAX) . "! Please add some XP to your xpbank using /xb add");
        } else {
            $user->getPlayer()->getXpManager()->addXp($xp, false);
        }
    }

    /**
     * @param Item   $item
     * @param string $name
     *
     * @return Item
     */
    public function renameItem(Item $item, string $name = "") : Item {
        if ($item->hasCustomName()) {
            return $this->setEnchantmentNames($item, $name);
        }
        $nwit = clone $item;
        if (!$item->hasEnchantments()) {
            if ($item instanceof Durable && $nwit instanceof Durable) {
                $nwit->setDamage($item->getDamage());
                $nwit->setLore($item->getLore());
                if ($name !== "")
                    $nwit->setCustomName(TF::RESET . TF::RED . $name);
                else $nwit->clearCustomName();
            }
        } else {
            return $this->setEnchantmentNames($item, $name);
        }
        return $nwit;
    }

    /**
     * @param Item $item
     *
     * @return string
     */
    public function getCleanName(Item $item) : string {
        return (($item->hasCustomName()) ? explode("\n", $item->getCustomName())[0] ?? $item->getCustomName() : $item->getVanillaName()) . TF::RESET;
    }

    /**
     * @param Player $player
     * @param int    $aucId
     * @param string $name
     *
     * @return bool
     */
    public function takeOffAuction(Player $player, int $aucId, string $name) : bool {
        if (isset($this->pl->auctions[$aucId])) {
            $auction = $this->pl->auctions[$aucId];
            $user = $auction['seller'];
            if ($user != $name) {
                $this->sendMessage($player, "§4[Error]§c That is not your Auction to take off.");
                return false;
            } else {
                $player->getInventory()->addItem(Item::nbtDeserialize($auction['item']));
                $this->sendMessage($player, '§eYou have successfully taken off your item ' . TF::AQUA . "$aucId" . TF::GREEN . ' from Auction.');
                unset($this->pl->auctions[$aucId]);
            }
        } else {
            $this->sendMessage($player, '§4[Error]§c The auction with the ID ' . $aucId . ' cannot be found.');
        }
        return true;
    }

    /**
     * @return string
     */
    public function getBook() : string {
        $l = mt_rand(1, 850);
        $le = '';
        if ($l <= 800) {
            $le = '§6Common';
        }
        if ($l > 800 and $l <= 850) {
            $le = '§6Rare';
        }
        return $le;
    }

    /**
     * @param string $cename
     *
     * @return array|null
     */
    public function getEnchantmentData(string $cename) : ?array {
        $cename = TF::clean($cename);
        $enchantments = $this->pl->getEnchantments();
        foreach ($enchantments as $data) {
            if (strtolower($data[0]) == strtolower($cename)) {
                return $data;
            }
        }
        return null;
    }

    /**
     * @param Player $sender
     * @param Player $player
     */
    public function fixPlayerHand(Player $sender, Player $player) : void {
        if ($player->getWorld()->getDisplayName() == Values::PVP_WORLD) {
            $this->sendMessage($player, "§cYou're in PvP world.§r");
            return;
        }
        $item = $player->getInventory()->getItemInHand();
        if (!$item instanceof Durable || (/*$item->getTypeId() !== 444 and*/ !$item instanceof Armor and !$item instanceof Tool)) { // TODO: Fix me
            // Elytra needs to be implemented.
            $this->sendMessage($player, "§4[Error]§c You're not holding a Tool, Armor or Wing in hand!");
            unset(Fix::$fixConfirm[$player->getName()]);
            return;
        }
        if ($item->getDamage() === 0) {
            $this->sendMessage($player, "§4[Error]§c That item is already brand new!");
            unset(Fix::$fixConfirm[$player->getName()]);
            return;
        }
        $new = 1;
        $max = Values::MAX_DEFAULT_FIX;
        if (($fixlore = Lore::getLoreInfo($item->getLore(), Values::FIX_LORE, Lore::FIX_STR)) !== null) {
            $data = explode("/", $fixlore);
            [$cur, $max] = $data;
            if ($cur >= $max) {
                $this->sendMessage($player, "§4[Error]§c That item has already been fixed Max - $max times! Check item info");
                unset(Fix::$fixConfirm[$player->getName()]);
                return;
            }
            $cur = (int) $cur;
            $new = $cur + 1;
        }
        $user = $this->pl->getUserManager()->getOnlineUser($sender->getName());
        $cost = Values::PER_ITEM_FIX_COST;
        if (!$user->removeMoney($cost)) {
            $this->sendMessage($sender, "§4[Error]§c You do not have enough money to fix the Item! Need §6$cost$");
            unset(Fix::$fixConfirm[$player->getName()]);
            return;
        }
        unset(Fix::$fixConfirm[$player->getName()]);
        $item->setDamage(0);
        Lore::setLoreInfo($item, Values::FIX_LORE, Lore::FIX_STR . "$new/$max");
        $player->getInventory()->setItemInHand($item);
        if ($sender->getXuid() !== $player->getXuid()) {
            $this->sendMessage($player, "§eYour held item was fixed for §6$cost$ §eby §a{$sender->getName()}");
            $this->sendMessage($sender, "§a{$player->getName()}§e's held item was fixed for §6$cost$");
        } else {
            $this->sendMessage($sender, "§eYour held item was fixed for §6$cost$");
        }
    }

    /**
     * @return Item
     */
    public function getRandomKey() : Item {
        $i = mt_rand(0, 3);
        if ($i == 0) $choice = 'vote';
        elseif ($i == 1) $choice = 'common';
        elseif ($i == 2) $choice = 'rare';
        else    $choice = 'legendary';
        return $this->pl->getCrateKeys($choice, mt_rand(1, 3));
    }

    /**
     * @return Item
     */
    public function getEnvoyItem1() : Item {
        $i = mt_rand(0, 6);
        if ($i == 0) $item = $this->pl->getScrolls();
        elseif ($i == 1) $item = $this->pl->getScrolls('enchanter');
        elseif ($i == 2) $item = $this->pl->getTagManager()->getRandomTag();
        elseif ($i == 3) {
            $item = $this->pl->getCrateKeys('rare');
        } else if ($i == 4) $item = $this->pl->getScrolls('inferno');
        else {
            $item = $this->pl->getTrollItem('lol');
        }
        return $item;
    }

    /**
     * @return Item
     */
    public function getEnvoyItem2() : Item {
        $i = mt_rand(0, 6);
        if ($i == 1 || $i == 2) $item = $this->opSword($i);
        elseif ($i == 3 || $i == 4) $item = LegacyStringToItemParser::getInstance()->parse(466)->setCount(mt_rand(1, 2));
        else {
            $item = $this->pl->getTrollItem('lmao');
        }
        return $item;
    }

    /**
     * @param int $level
     *
     * @return Item
     */
    public function opSword(int $level) : Item {
        $content
            = [
            "1" => ["Common" => ["times" => 2, "level" => mt_rand(1, 2)], "Rare" => ["times" => 1, "level" => mt_rand(1, 2)], "Legendary" => ["times" => 0, "level" => 0], "Exclusive" => ["times" => 0, "level" => 0]],
            "2" => ["Common" => ["times" => 2, "level" => mt_rand(2, 4)], "Rare" => ["times" => 2, "level" => mt_rand(1, 3)], "Legendary" => ["times" => 0, "level" => 0], "Exclusive" => ["times" => 0, "level" => 0]],
            "3" => ["Common" => ["times" => 2, "level" => mt_rand(3, 4)], "Rare" => ["times" => 1, "level" => mt_rand(3, 4)], "Legendary" => ["times" => 1, "level" => mt_rand(1, 2)], "Exclusive" => ["times" => 0, "level" => 0]],
            "4" => ["Common" => ["times" => 1, "level" => mt_rand(4, 5)], "Rare" => ["times" => 2, "level" => mt_rand(4, 5)], "Legendary" => ["times" => 1, "level" => mt_rand(3, 4)], "Exclusive" => ["times" => 0, "level" => 0]],
            "5" => ["Common" => ["times" => 1, "level" => mt_rand(5, 6)], "Rare" => ["times" => 2, "level" => mt_rand(5, 6)], "Legendary" => ["times" => 1, "level" => mt_rand(4, 6)], "Exclusive" => ["times" => 0, "level" => 0]],
            "6" => ["Common" => ["times" => 0, "level" => 0], "Rare" => ["times" => 2, "level" => mt_rand(5, 6)], "Legendary" => ["times" => 1, "level" => mt_rand(5, 6)], "Exclusive" => ["times" => 1, "level" => mt_rand(1, 3)]]
        ];
        $rarity = ["Common" => 0, "Rare" => 0, "Legendary" => 0, "Exclusive" => 0, "Ancient" => 0];
        $item = VanillaItems::DIAMOND_SWORD();
        foreach ($this->getRandomEnchantments() as $key => $ench) {
            $rare = $ench[3];
            if ($rare === "Ancient") {
                continue;
            }
            $type = $ench[4];
            if ($rarity["$rare"] >= $content[$level]["$rare"]["times"]) continue;
            if ($type !== "Sword") continue;
            else if ($this->pl->isVaulted($key)) continue;
            else {
                $rarity["$rare"]++;
                $enchantment = BaseEnchantment::getEnchantment($key);
                $item->addEnchantment(new EnchantmentInstance($enchantment, $content[$level]["$rare"]["level"]));
            }
        }
        return $this->setEnchantmentNames($item, false);
    }

    /**
     * @param Item $it
     * @param      $name
     *
     * @return Item
     */
    public function setEnchantmentNames(Item $it, $name) : Item {
        if ($name === "") $name = $it->getVanillaName();
        if (!$it instanceof Durable || !$it instanceof Durable) return VanillaItems::AIR();
        $nwit = clone $it;
        $nwit->setDamage($it->getDamage());
        $nwit->setLore($it->getLore());
        $str = "";
        if ($it->hasCustomName()) {
            $str = strtok($it->getCustomName(), "\n");
        }
        if ($it->hasEnchantments() and $it->hasCustomName() and $name === false) {
            $nwit->setCustomName(TextFormat::RESET . TextFormat::RED . $str . "\n" . TextFormat::GREEN);
        }
        if ($it->hasEnchantments() and !$it->hasCustomName()) {
            $nwit->setCustomName(TextFormat::RESET . TextFormat::RED . $it->getName() . "\n" . TextFormat::GREEN);
        }
        if (!$it->hasEnchantments() and !$it->hasCustomName()) {
            return $nwit;
        }
        if (!$it->hasEnchantments() and $it->hasCustomName()) {
            $nwit->setCustomName(TextFormat::RESET . TextFormat::RED . $name . "\n" . TextFormat::GREEN);
            return $nwit;
        }
        if ($name !== false) {
            $nwit->setCustomName(TextFormat::RESET . TextFormat::RED . $name . "\n" . TextFormat::GREEN);
        }
        foreach ($it->getEnchantments() as $enchant) {
            if (BaseEnchantment::getEnchantmentId($enchant) < 100) {
                continue;
            }
            $enchantments = $this->pl->getEnchantments();
            if (isset($enchantments[BaseEnchantment::getEnchantmentId($enchant)])) {
                $rarity = $enchantments[BaseEnchantment::getEnchantmentId($enchant)][3];
                $nwit->setCustomName($nwit->getCustomName() . TextFormat::RESET . "{$this->getColorForEnchant($rarity)}" . $enchant->getType()->getName() . " " . $this->getStringLevel($enchant) . "\n");
                $nwit->setCustomName(str_replace("\n\n", "\n", $nwit->getCustomName()));
            }
        }

        foreach ($it->getEnchantments() as $ench)
            $nwit->addEnchantment(new EnchantmentInstance($ench->getType(), $ench->getLevel()));

        return $nwit;
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
            "ancient" => "§1",
            default => '',
        };
    }

    /**
     * @param EnchantmentInstance $ench
     *
     * @return string
     */
    public function getStringLevel(EnchantmentInstance $ench) : string {
        $integer = $ench->getLevel();
        $table = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $return = '';
        while ($integer > 0) {
            foreach ($table as $rom => $arb) {
                if ($integer >= $arb) {
                    $integer -= $arb;
                    $return .= $rom;
                    break;
                }
            }
        }
        return $return;
    }

    /**
     * @return Item
     */
    public function getEnvoyItem3() : Item {
        $i = mt_rand(0, 2);
        if ($i == 0) {
            $armor = [310, 302, 306, 311, 303, 307, 312, 304, 308, 313, 305, 309];
            $item = LegacyStringToItemParser::getInstance()->parse($armor[mt_rand(0, 11)]);
            if (($id = $this->randomCEType('armor')) === null) return $this->pl->getTrollItem('rip');
            $enchantment = BaseEnchantment::getEnchantment($id);
            $item->addEnchantment(new EnchantmentInstance($enchantment, mt_rand(1, 6)));
            return $this->setEnchantmentNames($item, false);
            //        } elseif ($i == 1) {
            //            return $this->pl->getFireworkItem();
        } else {
            return $this->pl->getTrollItem('rip');
        }
    }

    /**
     * @param string $type1
     * @param string $rarity
     *
     * @return int|null
     */
    public function randomCEType(string $type1 = 'sword', string $rarity = 'common') : ?int {
        foreach ($this->getRandomEnchantments() as $key => $ench) {
            if (!$this->pl->isVaulted($key) and $this->checkCompatibility($ench[4], $type1) and strtolower($ench[3]) === strtolower($rarity)) return $key;
        }
        return null;
    }

    /**
     * @param string $type1
     *
     * @return int|null
     */
    public function randomCE(string $type1 = 'sword') : ?int {
        foreach ($this->getRandomEnchantments() as $key => $ench) {
            if ($this->checkCompatibility($ench[4], $type1)) return $key;
        }
        return null;
    }

    /**
     * @return array
     */
    public function getRandomEnchantments() : array {
        $enchants = $this->pl->getEnchantments();
        $keys = array_keys($enchants);
        shuffle($keys);
        $random = [];
        foreach ($keys as $key)
            $random[$key] = $enchants[$key];
        return $random;
    }

    /**
     * @return Item
     */
    public function getEnvoyItem4() : Item {
        $random = mt_rand(0, 2);
        if ($random === 0) {
            $item = $this->getRandomKit();
        } else {
            if (mt_rand(1, 100) === 1) {
                $item = Main::getInstance()->getCrateKeys("mystic");
            } else {
                $item = Main::getInstance()->getTrollItem("troll");
            }
        }
        return $item;
    }

    /**
     * @return Item
     */
    public function getRandomKit() : Item {
        $kits = ["guest", "king", "vip", "myth", "skylord", "skygod", "skyzeus"];
        $randKey = array_rand($kits, 1);
        $kits = Main::getInstance()->getKit($kits[$randKey]);
        if ($kits !== null) {
            $chest = $kits->getKitChest();
        } else {
            $chest = VanillaBlocks::CHEST()->asItem();
        }
        return $chest;
    }

    /**
     * @return Item
     */
    public function getEnvoyItem5() : Item {
        $i = mt_rand(0, 5); // todo
        if ($i == 0) $item = $this->pl->getCEBook($this->getEnvoyBook());
        elseif ($i == 1) $item = $this->getRandomKit();
        elseif ($i == 2) $item = $this->pl->getTagManager()->getRandomTag();
        else $item = $this->pl->getTrollItem('troll');
        return $item;
    }

    /**
     * @return string
     */
    public function getEnvoyBook() : string {
        $l = mt_rand(1, 2000);
        $le = '';
        if ($l <= 800) {
            $le = '§6Common';
        }
        if ($l > 800 and $l <= 1500) {
            $le = '§6Rare';
        }
        if ($l > 1500 and $l <= 1900) {
            $le = '§6Legendary';
        }
        if ($l > 1900 and $l <= 2000) {
            $le = '§6Exclusive';
        }
        return $le;
    }

    /**
     * @param World $level
     */
    public function clearEntities(World $level) : void {
        foreach ($level->getEntities() as $entity) {
            if (!($entity instanceof Human) && !$entity instanceof Painting) {
                $entity->close();
            }
        }
    }

    public function clearAllEntities() : void {
        foreach ($this->pl->getServer()->getWorldManager()->getWorlds() as $level) {
            foreach ($level->getEntities() as $entity) {
                if (!($entity instanceof Human) && !$entity instanceof Painting) {
                    $entity->close();
                }
            }
        }
    }

    /**
     * @return int
     */
    public function removeEntities() : int {
        $i = 0;
        foreach ($this->pl->getServer()->getWorldManager()->getWorlds() as $level) {
            foreach ($level->getEntities() as $entity) {
                if (!($entity instanceof Human) && !($entity instanceof ItemEntity) && !($entity instanceof Creature) && !$entity instanceof Painting) {
                    $entity->close();
                    $i++;
                }
            }
        }
        return $i;
    }

    /**
     * @return int
     */
    public function removeMobs() : int {
        $i = 0;
        foreach ($this->pl->getServer()->getWorldManager()->getWorlds() as $level) {
            foreach ($level->getEntities() as $entity) {
                if (!$entity instanceof BasePet && $entity instanceof Creature) {
                    $entity->close();
                    $i++;
                }
            }
        }
        return $i;
    }

    /**
     * @return array
     */
    public function getEntityCount() : array {
        $ret = [0, 0, 0];
        foreach ($this->pl->getServer()->getWorldManager()->getWorlds() as $level) {
            foreach ($level->getEntities() as $entity) {
                if ($entity instanceof Human) {
                    $ret[0]++;
                } else if ($entity instanceof Creature) {
                    $ret[1]++;
                } else {
                    $ret[2]++;
                }
            }
        }
        return $ret;
    }

    /**
     * @return array
     */
    public function getItemEntityCount() : array {
        $ret = [];
        foreach ($this->pl->getServer()->getWorldManager()->getWorlds() as $level) {
            foreach ($level->getEntities() as $entity) {
                if ($entity instanceof ItemEntity) {
                    if (!isset($ret[$level->getDisplayName()])) $ret[$level->getDisplayName()] = 1;
                    else $ret[$level->getDisplayName()] += 1;
                }
            }
        }
        return $ret;
    }

    /**
     * @param array $entities
     *
     * @return int
     */
    public function getIECount(array $entities) : int {
        return count(array_filter($entities, function(Entity $entity) {
            return $entity->isAlive() and !$entity->isClosed() and $entity instanceof ItemEntity;
        }
                     )
        );
    }

    /**
     * @return array
     */
    public function getPreciseEntityCount() : array {
        $ret['creature'] = 0;
        $ret['arrow'] = 0;
        $ret['splash'] = 0;
        $ret['ptnt'] = 0;
        $ret['epearl'] = 0;
        $ret['snowball'] = 0;
        $ret['egg'] = 0;
        $ret['exporb'] = 0;
        $ret['itementity'] = 0;
        $ret['basepet'] = 0;
        $ret['fblock'] = 0;
        $ret['other'] = 0;
        foreach ($this->pl->getServer()->getWorldManager()->getWorlds() as $level) {
            foreach ($level->getEntities() as $entity) {
                if ($entity instanceof Human) continue;
                if ($entity instanceof BasePet) {
                    $ret['basepet']++;
                } else if ($entity instanceof Creature) {
                    $ret['creature']++;
                } else if ($entity instanceof Arrow) {
                    $ret['arrow']++;
                } else if ($entity instanceof SplashPotion) {
                    $ret['splash']++;
                } else if ($entity instanceof PrimedTNT) {
                    $ret['ptnt']++;
                } else if ($entity instanceof EnderPearl) {
                    $ret['epearl']++;
                } else if ($entity instanceof Snowball) {
                    $ret['snowball']++;
                } else if ($entity instanceof Egg) {
                    $ret['egg']++;
                } else if ($entity instanceof ExperienceOrb) {
                    $ret['exporb']++;
                } else if ($entity instanceof ItemEntity) {
                    $ret['itementity']++;
                } else if ($entity instanceof FallingBlock) {
                    $ret['fblock']++;
                } else {
                    $ret['other']++;
                }
            }
        }
        return $ret;
    }

    /**
     * @param int    $level
     * @param string $type
     *
     * @return Item
     */
    public function gkitItems(int $level, string $type = 'sword') : Item {
        $gtype = strtolower($type);
        $content
            = [
            "1" => ["Common" => ["times" => 2, "level" => mt_rand(4, 5)], "Rare" => ["times" => 1, "level" => mt_rand(4, 5)], "Legendary" => ["times" => 0, "level" => 0], "Exclusive" => ["times" => 0, "level" => 0]],
            "2" => ["Common" => ["times" => 2, "level" => mt_rand(5, 6)], "Rare" => ["times" => 2, "level" => mt_rand(5, 6)], "Legendary" => ["times" => 0, "level" => 0], "Exclusive" => ["times" => 0, "level" => 0]],
            "3" => ["Common" => ["times" => 2, "level" => mt_rand(6, 7)], "Rare" => ["times" => 1, "level" => mt_rand(6, 7)], "Legendary" => ["times" => 1, "level" => mt_rand(4, 5)], "Exclusive" => ["times" => 0, "level" => 0]],
            "4" => ["Common" => ["times" => 1, "level" => mt_rand(7, 9)], "Rare" => ["times" => 2, "level" => mt_rand(7, 8)], "Legendary" => ["times" => 1, "level" => mt_rand(5, 6)], "Exclusive" => ["times" => 1, "level" => mt_rand(2, 4)]],
            "5" => ["Common" => ["times" => 1, "level" => mt_rand(9, 10)], "Rare" => ["times" => 2, "level" => mt_rand(8, 9)], "Legendary" => ["times" => 1, "level" => mt_rand(7, 8)], "Exclusive" => ["times" => 1, "level" => mt_rand(4, 6)]],
            "6" => ["Common" => ["times" => 1, "level" => 10], "Rare" => ["times" => 2, "level" => mt_rand(9, 10)], "Legendary" => ["times" => 1, "level" => mt_rand(9, 10)], "Exclusive" => ["times" => 1, "level" => mt_rand(7, 8)]]
        ];
        $choose = "";
        $item = null;
        $rarity = ["Common" => 0, "Rare" => 0, "Legendary" => 0, "Exclusive" => 0];
        switch ($gtype) {
            case 'sword':
                $choose = 'Sword';
                $item = LegacyStringToItemParser::getInstance()->parse(276);
                break;
            case 'helmet':
                $choose = 'Armor';
                $item = LegacyStringToItemParser::getInstance()->parse(310);
                break;
            case 'chestplate':
                $choose = 'Armor';
                $item = LegacyStringToItemParser::getInstance()->parse(311);
                break;
            case 'leggings':
                $choose = 'Armor';
                $item = LegacyStringToItemParser::getInstance()->parse(312);
                break;
            case 'boots':
                $choose = 'Armor';
                $item = LegacyStringToItemParser::getInstance()->parse(313);
                break;
            case 'random':
                $i = mt_rand(0, 1);
                if ($i == 0) $choose = 'Armor';
                else $choose = 'Sword';
                $gtype = $choose;
                if ($choose == 'Sword') $item = LegacyStringToItemParser::getInstance()->parse(276);
                else {
                    $item = LegacyStringToItemParser::getInstance()->parse(mt_rand(310, 313));
                }
                break;
        }
        foreach ($this->getRandomEnchantments() as $key => $ench) {
            $rare = $ench[3];
            if ($rare === "Ancient") {
                continue;
            }
            if ($rarity["$rare"] >= $content[$level]["$rare"]["times"]) continue;
            if (!$this->checkCompatibility($ench[4], $gtype)) continue;
            else if ($this->pl->isVaulted($key)) continue;
            else {
                $rarity["$rare"]++;
                $enchantment = BaseEnchantment::getEnchantment($key);
                $item->addEnchantment(new EnchantmentInstance($enchantment, $content[$level]["$rare"]["level"]));
            }
        }
        $hand = $this->setEnchantmentNames($item, false);
        $enchantids = ['Sword' => [9, 12, 13, 14, 17], 'Armor' => [0, 1, 3, 4, 17]];
        $levels = [1 => ['level' => mt_rand(5, 8), 'times' => 2], 2 => ['level' => mt_rand(6, 8), 'times' => 2], 3 => ['level' => mt_rand(7, 8), 'times' => 3], 4 => ['level' => mt_rand(7, 9), 'times' => 3], 5 => ['level' => mt_rand(8, 10), 'times' => 4], 6 => ['level' => mt_rand(9, 10), 'times' => 4]];
        $times = $levels[$level]['times'];
        $lev = $levels[$level]['level'];
        $enchants = $enchantids[$choose];
        $ids = array_rand($enchants, $times);
        foreach ($ids as $key) {
            $enchantid = $enchants[$key];
            $enchantment = BaseEnchantment::getEnchantment($enchantid);
            $hand->addEnchantment(new EnchantmentInstance($enchantment, $lev));
        }
        return $hand;
    }

    /**
     * @param $number
     *
     * @return string
     */
    public function numberToEnchantment($number) : string {
        return match ($number) {
            0 => 'Protection',
            1 => 'Fire Protection',
            2 => 'Feather Falling',
            3 => 'Blast Protection',
            4 => 'Projectile Protection',
            5 => 'Thorns',
            6 => 'Respiration',
            7 => 'Depth Strider',
            8 => 'Aqua Affinity',
            9 => 'Sharpness',
            10 => 'Smite',
            11 => 'Bane of Arthropods',
            12 => 'Knockback',
            13 => 'Fire Aspect',
            14 => 'Looting',
            15 => 'Efficiency',
            16 => 'Silk Touch',
            17 => 'Unbreaking',
            18 => 'Fortune',
            19 => 'Bow Power',
            20 => 'Bow Knockback',
            21 => 'Bow Flame',
            22 => 'Bow Infinity',
            default => 'N/A',
        };
    }

    /**
     * @param $damage
     *
     * @return string
     */
    public function getPotionName($damage) : string {
        return match ($damage) {
            1 => "Mundane Potion",
            4 => "Awkward Potion",
            5 => "NightVision Potion(3:00)",
            7 => "Invisibility Potion(3:00)",
            9 => "Leaping Potion(3:00)",
            12 => "FireResistance Potion(3:00)",
            14 => "Swiftness Potion(3:00)",
            17 => "Slowness Potion(3:00)",
            19 => "WaterBreathing Potion(3:00)",
            28 => "Regeneration Potion(3:00)",
            31 => "Strength Potion(3:00)",
            34 => "Weakness Potion(3:00)",
            default => "",
        };
    }

    /**
     * @param Player $player
     * @param string $kit
     */
    public function giveKitChest(Player $player, string $kit) : void {
        $gkits = ['achilles' => 1, 'theo' => 2, 'cosmo' => 3, 'arcadia' => 4, 'artemis' => 5, 'calisto' => 6];
        $tag = [];
        $i = 0;
        $item = $this->pl->getFunctions()->gkitItems($gkits[$kit], 'sword');
        $tag[$i] = $item->nbtSerialize($i++);
        $item = $this->pl->getFunctions()->gkitItems($gkits[$kit], 'helmet');
        $tag[$i] = $item->nbtSerialize($i++);
        $item = $this->pl->getFunctions()->gkitItems($gkits[$kit], 'chestplate');
        $tag[$i] = $item->nbtSerialize($i++);
        $item = $this->pl->getFunctions()->gkitItems($gkits[$kit], 'leggings');
        $tag[$i] = $item->nbtSerialize($i++);
        $item = $this->pl->getFunctions()->gkitItems($gkits[$kit], 'boots');
        $tag[$i] = $item->nbtSerialize($i++);
        $item = $this->pl->getScrolls('levelup');
        $tag[$i] = $item->nbtSerialize($i++);
        $item = $this->pl->getScrolls('enchanter');
        $tag[$i] = $item->nbtSerialize($i++);
        $item = $this->pl->getScrolls('inferno');
        $tag[$i] = $item->nbtSerialize($i++);
        $item = $this->pl->getScrolls('god');
        $tag[$i] = $item->nbtSerialize($i++);
        $item = VanillaItems::ENCHANTED_GOLDEN_APPLE()->setCount(mt_rand(3, 5));
        $item = LegacyStringToItemParser::getInstance()->parse(466)->setCount(mt_rand(1, 3));
        $tag[$i] = $item->nbtSerialize($i++);

        $ctag = new CompoundTag();
        $ctag->setTag(Container::TAG_ITEMS, new ListTag($tag, NBT::TAG_Compound));
        $chest = VanillaBlocks::CHEST()->asItem();
        $chest->setNamedTag($chest->getNamedTag());
        $chest->setCustomBlockData($ctag);
        $name = ucfirst($kit);
        $chest->setCustomName("§o§l§b{$name} §fGKit\n§r§ePlace this chest\n§eand open it to get the items!");
        $player->getInventory()->addItem($chest);
    }

    /**
     * @param Player $player
     * @param int    $offset
     *
     * @return bool
     */
    public function isInventoryFull(Player $player, int $offset = 1) : bool {
        $full = true;
        $y = 0;
        for ($i = 0; $i < $player->getInventory()->getSize(); $i++) {
            if ($player->getInventory()->getItem($i)->getTypeId() == VanillaItems::AIR()->getTypeId()) {
                ++$y;
                if ($y >= $offset) $full = false;
            }
        }
        return $full;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function getColorForType(string $type) : string {
        return match (strtolower($type)) {
            "axe" => '§f',
            "pickaxe" => '§d',
            "sword", "tool" => '§a',
            "bow" => '§c',
            "armor" => '§e',
            "helmet", "chestplate", "leggings", "boots" => '§6',
            default => '',
        };
    }

    public static function calcTinkBarterMoneyXp(int $level) : int {
        return match ($level) {
            11 => 1,
            12 => 2,
            13 => 3,
            14 => 4,
            16 => 5,
            default => 0
        };
    }

}