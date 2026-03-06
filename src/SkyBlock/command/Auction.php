<?php


namespace SkyBlock\command;


use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\item\Tool;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\Main;

class Auction extends BaseCommand {
    /**
     * Auction constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'ah', 'Auction House Help', 'help', true, ['auction', 'auctions']);
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!isset($args[0]) or !$sender instanceof Player) {
            $this->sendUsage($sender, $commandLabel);
            return;
        }
        if ($this->pl->isInCombat($sender)) {
            $this->sendMessage($sender, "§cYou're in combat.");
            return;
        }
        $orig = $args[1] ?? null;
        if (isset($args[1])) {
            $args[1] = (int) $args[1];
        }
        switch (strtolower($args[0])) {
            case "brag":
                if (!isset($args[1]) or isset($args[2])) {
                    $this->sendMessage($sender, TF::AQUA . '/ah brag ' . TF::GRAY . '<AuctionID>' . "\n" . TF::GRAY . 'Brag the item on our discord channel §ahttps://discord.fallentech.io');
                    break;
                } else {
                    if (!is_int((int) $args[1])) {
                        $this->sendMessage($sender, "§6Usage: /ah brag <Auctionid>");
                        break;
                    }
                    $args[1] = (int) $args[1];
                    if (isset($this->pl->bragauction[strtolower($sender->getName())])) {
                        $this->sendMessage($sender, "§4[Error]§c You have already bragged once this restart, try next restart!");
                        break;
                    }
                    $aucId = (int) $args[1];
                    if (isset($this->pl->auctions[$aucId])) {
                        $auction = $this->pl->auctions[$aucId];
                        $auc = $auction;
                        $user = $auction['seller'];
                        if (strtolower($user) != strtolower($sender->getName())) {
                            $this->sendMessage($sender, "§4[Error]§c That is not your Auction to brag.");
                            break;
                        } else {
                            $message = "";
                            $item = Item::nbtDeserialize($auc['item']);
                            $bb = "[+]" . str_repeat('=', 15) . "[+]";
                            $message .= "\n" . $bb;
                            $used = $auc['damage'] > 0 ? 'Yes' : 'No';
                            $enchanted = $item->hasEnchantments() ? 'Yes' : 'No';
                            $message .= "\n**ID**: `" . $aucId . "`\n**Item**: `" . $auc['name'] . "`" . "\n**Count**: `" . $auc['count'] . "`\n**Used Item**: `" . $used . "`\n**Enchanted**: `" . $enchanted . "`\n**Cost**: `" . number_format($auc['price']) . "`$\n**Seller**: `" . $auc['seller'] . "`";
                            if ($enchanted == 'Yes') {
                                $message .= "\n**Enchantments**:";
                                $itemens = $item->getEnchantments();
                                foreach ($itemens as $enchant) {
                                    $en = BaseEnchantment::getEnchantmentId($enchant);
                                    if ($en < 25) {
                                        $name = $this->func->numberToEnchantment($en);
                                        $lev = $enchant->getLevel();
                                        $message .= "\n- `" . $name . "` " . $lev;
                                    }
                                    if ($en > 99) {
                                        $name = BaseEnchantment::getEnchantment($en)->getName();
                                        $lev = $enchant->getLevel();
                                        $message .= "\n- `" . $name . "` " . $lev;
                                    }
                                }
                            }
                            $message .= "\n" . $bb;
                            $this->pl->bragauction[strtolower($sender->getName())] = true;
                            $this->sendMessage($sender, "§eYou have successfully bragged about your auction item §a$aucId §ein our discord channel!\n§eJoin now: §bhttps://discord.fallentech.io §eand support the community!");
                            $this->pl->sendDiscordMessage(":postal_horn: Auction Info :postal_horn:", $message, 2);
                        }
                    } else {
                        $this->sendMessage($sender, '§4[Error]§c The auction with the ID ' . $aucId . ' cannot be found.');
                    }
                }
                break;
            case "sell":
                if (isset($args[1])) {
                    $item = $sender->getInventory()->getItemInHand();
                    if ($item === null || $item->isNull()) {
                        $this->sendMessage($sender, "§4[Error]§c Invalid item!");
                        break;
                    }
                    if ($item->getTypeId() !== BlockTypeIds::AIR and $item->getTypeId() !== BlockTypeIds::CHEST) { // chests
                        $price = (int) $args[1];
                        if ($price <= 0 or $args[1] > 500000000 or !is_int((int) $price)) {
                            $this->sendMessage($sender, "§4[Error]§c Sell price is to be greater than 0 and less than or equal to 500 million!");
                            break;
                        }
                        $price = (int) $price;
                        $name = strtolower($sender->getName());
                        $total = $this->func->userSellCount($name);
                        $slots = 5;
                        $str = "";
                        if ($sender->hasPermission("core.ah.8")) $slots = 8;
                        else $str = "\n§7Unlock more slots at §aSkyWARRIOR §eRank, buy from §bshop.fallentech.io!";
                        if ($total >= $slots) {
                            $this->sendMessage($sender, "§4[Error]§c You can only place $slots items on auction at a time, you can take off your items off auction by /ah takeoff <auction Id>" . $str);
                            break;
                        }

                        if ($item instanceof Armor or $item instanceof Tool) $item = $this->func->renameItem($item, "");

                        $iname = $this->func->getCleanName($item);
                        $auctiondata['item'] = $item->nbtSerialize();
                        $auctiondata['count'] = $item->getCount() ?? 1;
                        $auctiondata['damage'] = $auctiondata['damage'] ?? 0;
                        $auctiondata['name'] = $iname;
                        $auctiondata['seller'] = $name;
                        $auctiondata['price'] = $price;

                        $aucId = $this->func->getFreeKey();
                        $this->pl->auctions[$aucId] = $auctiondata;
                        $this->sendMessage($sender, '§eYou have successfully placed your §e' . $iname . ' §7(§cx§e' . $item->getCount() . '§7) §efor §6$' . $price . ' §eon auction.');
                        $this->sendMessage($sender, TF::YELLOW . 'Your auction ID is ' . TF::GREEN . $aucId . TF::GRAY . '.');
                        $sender->sendMessage(TF::GOLD . 'Do /ah brag to advertise your auction on our discord server: §ahttps://discord.fallentech.io');
                        $sender->getInventory()->setItemInHand(VanillaItems::AIR());
                    } else {
                        $this->sendMessage($sender, TF::RED . 'Item not valid to put on auction!');
                    }
                } else {
                    $this->sendMessage($sender, TF::AQUA . '/ah sell ' . TF::GRAY . '<price>' . "\n" . TF::GRAY . 'Put the item you are currently holding, in auction for ' . TF::YELLOW . '$<price>');
                }
                break;

            case 'takeoff':
                if (isset($args[1])) {
                    if (!is_int((int) $args[1])) {
                        $this->sendMessage($sender, "§6Usage: /ah takeoff <auc id>");
                        break;
                    }
                    $args[1] = (int) $args[1];
                    $nam = strtolower($sender->getName());
                    if ($this->func->isInventoryFull($sender)) {
                        $this->sendMessage($sender, "§4[Error]§c Your inventory is full! Empty a slot first.");
                        break;
                    }
                    $this->func->takeOffAuction($sender, (int) $args[1], $nam);
                } else {
                    $this->sendMessage($sender, TF::AQUA . '/ah takeoff ' . TF::GRAY . '<AuctionID>' . "\n" . TF::GRAY . 'Take your ' . TF::YELLOW . '<AuctionID>' . TF::GRAY . ' from auction.');
                }

                break;

            case 'buy':
                if (isset($args[1])) {
                    $play = strtolower($sender->getName());
                    if (!is_int((int) $args[1])) {
                        $this->sendMessage($sender, "§6Usage: /ah buy <Auctionid>");
                        break;
                    }
                    $args[1] = (int) $args[1];
                    if ($this->func->isInventoryFull($sender)) {
                        $this->sendMessage($sender, "§4[Error]§c Your inventory is full! Empty a slot before purchasing.");
                        break;
                    }
                    $this->func->buyAuction($args[1], $sender, $play, $args);
                } else {
                    $this->sendMessage($sender, TF::AQUA . '/ah buy ' . TF::GRAY . '<AuctionID>' . "\n" . TF::GRAY . 'Buy the item assigned ' . TF::YELLOW . '<AuctionID>' . TF::GRAY . ' off auction.');
                }
                break;

            case 'user':

                if (!isset($args[1])) {
                    $this->sendMessage($sender, TF::GRAY . 'Use ' . TF::YELLOW . '/ah user <sellername> <page>' . TF::GRAY . ' to find item by seller!');
                } else {
                    $expected = strtolower($orig);
                    if (isset($args[2])) {
                        if (!is_int((int) $args[2]) || $args[2] <= 0) {
                            $this->sendMessage($sender, TF::RED . "Please enter a value greater than 0!");
                            break;
                        }
                        $args[2] = (int) $args[2];
                        $this->func->sendUserAuctions($sender, $expected, $args[2]);
                    }
                    if (!isset($args[2]))
                        $this->func->sendUserAuctions($sender, $expected, 1);
                }

                break;

            case 'list':

                if (!isset($args[1])) {
                    $this->func->sendAuctionList($sender, 1);
                    $this->sendMessage($sender, TF::GRAY . 'Use ' . TF::YELLOW . '/ah info <AuctionID>' . TF::GRAY . ' to get more information about an item, ' . TF::YELLOW . '/ah user <sellername> <page> ' . TF::GRAY . 'to find item by seller,' . TF::YELLOW . ' /ah filter ' . TF::GRAY . 'to apply filters to the search.' . TF::YELLOW . ' /ah takeoff <AuctionID> ' . TF::GRAY . 'to take your item off Auction.');
                } else {
                    if (isset($args[1]) and is_int((int) $args[1])) {
                        if ($args[1] <= 0) {
                            $this->sendMessage($sender, "§4[Error]§c Please enter a value greater than 0!");
                            break;
                        }
                        $this->func->sendAuctionList($sender, (int) $args[1]);
                        $this->sendMessage($sender, TF::GRAY . 'Use ' . TF::YELLOW . '/ah info <AuctionID>' . TF::GRAY . ' to get more information about an item, ' . TF::YELLOW . '/ah user <sellername> <page> ' . TF::GRAY . 'to find item by seller,' . TF::YELLOW . ' /ah filter ' . TF::GRAY . 'to apply filters to the search.' . TF::YELLOW . ' /ah takeoff <AuctionID> ' . TF::GRAY . 'to take your item off Auction.');
                    }
                }

                break;

            case "filter":
            case "search":

                if (!isset($args[2])) {
                    $this->sendMessage($sender, "§6Usage: /ah filter --item [id] --enchant [ench name] --order [price] <asc/desc> <page>");
                    break;
                }
                array_shift($args);
                $itemkey = array_search("--item", $args, true);
                $itemid = "";
                $item = VanillaBlocks::AIR()->asItem();
                if ($itemkey !== false) {
                    if (!isset($args[$itemkey + 1])) {
                        $this->sendMessage($sender, "§6Usage: /ah filter --item <id>");
                        break;
                    }
                    $itemid = $args[$itemkey + 1];

                    try {
                        $item = LegacyStringToItemParser::getInstance()->parse($itemid) ?? StringToItemParser::getInstance()->parse($itemid);
                    } catch (\Exception) {
                        $this->sendMessage($sender, "§cInvalid item id/namespace specified. try 'Stone' or '1'!");
                        break;
                    }
                }
                $enchkey = array_search("--enchant", $args, true);
                $enchid = -1;
                if ($enchkey !== false) {
                    if (!isset($args[$enchkey + 1])) {
                        $this->sendMessage($sender, "§6Usage: /ah filter --enchant <id>");
                        break;
                    }
                    $enchname = $args[$enchkey + 1];
                    if (is_int((int) $enchname)) {
                        $this->sendMessage($sender, "§6Usage: /ah filter --item [id] --enchant [ench name] --order [price/id] --sort <asc/desc> --page <page>\nEnchant should be a enchant name not number/id!");
                        break;
                    }
                    if ($enchname != "") {
                        if (($enchid = $this->pl->getEnchantIdByName($enchname)) === null) {
                            $ench = BaseEnchantment::parse($enchname);
                            if ($ench instanceof EnchantmentInstance) {
                                $enchid = BaseEnchantment::getEnchantmentId($ench);
                            } else $enchid = false;
                        }
                        if (!$enchid or !is_int((int) $enchid)) {
                            $this->sendMessage($sender, "§6Usage: /ah filter --item [id] --enchant [ench name] --order [price/id] <asc/desc> <page>\nEnchant name not found, use underscore instead of space if vanilla enchant!");
                            break;
                        }
                    }
                }
                $enchid = (int) $enchid;
                $order = "id";
                if (($orderkey = array_search("--order", $args, true)) !== false) {
                    if (!isset($args[$orderkey + 1])) {
                        $this->sendMessage($sender, "§6Usage: /ah filter --item <id> --order <price/id>");
                        break;
                    }
                    $order = $args[$orderkey + 1];
                }
                if ($order != "id" and $order != "price") {
                    $this->sendMessage($sender, "§6Usage: /ah filter --item [id] --enchant [ench name] --order [price/id] <asc/desc> <page>\nOrder selection not found! Use id or price.");
                    break;
                }
                $sort = "asc";
                if (($sortkey = array_search("--sort", $args, true)) !== false) {
                    if (!isset($args[$sortkey + 1])) {
                        $this->sendMessage($sender, "§6Usage: /ah filter --item <id> --sort <asc/desc>");
                        break;
                    }
                    $sort = $args[$sortkey + 1];
                }
                if ($sort != "asc" and $sort != "desc") {
                    $this->sendMessage($sender, "§6Usage: /ah filter --item [id] --enchant [ench name] --order [price/id] <asc/desc> <page>\nSort selection not found! Use asc or desc.");
                    break;
                }
                $page = 1;
                if (($pagekey = array_search("--page", $args, true)) !== false) {
                    if (!isset($args[$pagekey + 1])) {
                        $this->sendMessage($sender, "§6Usage: /ah filter --item <id> --page <page id>");
                        break;
                    }
                    $page = $args[$pagekey + 1];
                }
                if (!is_int((int) $page) /*|| !is_numeric($itemid)*/) {
                    $this->sendMessage($sender, "§6Usage: /ah filter --item [id] --enchant [ench name] --order [price/id] <asc/desc> <page>\nSort selection not found! Page should be a number.");
                    break;
                }
                $page = (int) $page;
                try {
                    $this->func->sendFilteredAuctionList($sender, $this->func->getCleanName($item), $enchid, $order, $sort, $page);
                } catch (\Exception) {
                    $this->sendMessage($sender, "§cAn error occurred!");
                }
                break;

            case 'info':
                if (isset($args[1]) && is_int((int) $args[1])) {
                    $id = (int) $args[1];
                    if (isset($this->pl->auctions[$id])) {
                        $auc = $this->pl->auctions[$id];
                        $item = Item::nbtDeserialize($auc['item']);
                        $extra = implode(",", $item->getLore()) ?? "";
                        $bb = TF::YELLOW . '[+]' . TF::DARK_GRAY . str_repeat('=', 30) . TF::YELLOW . '[+]';
                        $used = $auc['damage'] > 0 ? 'Yes' : 'No';
                        $enchanted = $item->hasEnchantments() ? 'Yes' : 'No';
                        $this->sendMessage($sender, $bb);
                        if ($item instanceof Tool || $item instanceof Armor)
                            $sender->sendMessage(TF::AQUA . 'Item: ' . TF::YELLOW . $auc['name'] . TF::DARK_GRAY . "\n" . TF::RESET . TF::AQUA . 'Count: ' . TF::GREEN . $auc['count'] . "\n" .
                                                 TF::AQUA . 'ID: ' . TF::GREEN . $item->getVanillaName() . "\n" .
                                                 TF::AQUA . 'Used Item: ' . TF::GREEN . $used . "\n" .
                                                 TF::AQUA . 'Enchanted: ' . TF::GREEN . $enchanted . "\n" .
                                                 TF::AQUA . 'Cost: ' . TF::GREEN . number_format($auc['price']) . "$\n" .
                                                 TF::AQUA . 'Seller: ' . TF::GREEN . $auc['seller'] . "\n" .
                                                 TF::AQUA . 'Extra Data: §7' . $extra
                            );
                        else
                            $sender->sendMessage(TF::AQUA . 'Item: ' . TF::YELLOW . $auc['name'] . TF::DARK_GRAY . "\n" . TF::RESET . TF::AQUA . 'Count: ' . TF::GREEN . $auc['count'] . "\n" .
                                                 TF::AQUA . 'ID: ' . TF::GREEN . $item->getVanillaName() . ":" . $auc['damage'] . "\n" .
                                                 TF::AQUA . 'Enchanted: ' . TF::GREEN . $enchanted . "\n" .
                                                 TF::AQUA . 'Cost: ' . TF::GREEN . number_format($auc['price']) . "$\n" .
                                                 TF::AQUA . 'Seller: ' . TF::GREEN . $auc['seller'] . "\n" .
                                                 TF::AQUA . 'Extra Data: §7' . $extra
                            );
                        if ($enchanted == 'Yes') {
                            $sender->sendMessage(TF::AQUA . 'Enchantments:');
                            $itemens = $item->getEnchantments();
                            foreach ($itemens as $enchant) {
                                $en = BaseEnchantment::getEnchantmentId($enchant);
                                if ($en < 25) {
                                    $name = $this->func->numberToEnchantment($en);
                                    $lev = $enchant->getLevel();
                                    $sender->sendMessage(TF::YELLOW . "- " . $name . " " . TF::WHITE . $lev);
                                }
                                if ($en > 99 && $en < 175) {
                                    $name = BaseEnchantment::getEnchantment($en)->getName();
                                    $lev = $enchant->getLevel();
                                    $sender->sendMessage(TF::GREEN . "- " . $name . " " . TF::WHITE . $lev);
                                }
                                if ($en >= 175) {
                                    $name = BaseEnchantment::getEnchantment($en)->getName();
                                    $lev = $enchant->getLevel();
                                    $sender->sendMessage(TF::AQUA . "- " . $name . " " . TF::WHITE . $lev);
                                }
                            }
                        }
                        $this->sendMessage($sender, $bb);
                    } else {
                        $this->sendMessage($sender, '§4[Error]§c That Auction ID cannot be found.');
                    }
                } else {
                    $this->sendMessage($sender, TF::AQUA . '/ah info ' . TF::GRAY . '<AuctionID>');
                }
                break;

            default:
                $this->func->sendAuctionHelp($sender);
                break;

        }
    }
}