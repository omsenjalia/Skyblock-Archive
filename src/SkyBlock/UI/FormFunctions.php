<?php


namespace SkyBlock\UI;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\ModalForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use pocketmine\world\Position;
use pocketmine\world\World;
use SkyBlock\command\Functions;
use SkyBlock\command\Trade;
use SkyBlock\EvFunctions;
use SkyBlock\island\Island;
use SkyBlock\Main;
use SkyBlock\perms\PermissionManager;
use SkyBlock\tiles\AutoMinerTile;
use SkyBlock\tiles\AutoSellerTile;
use SkyBlock\tiles\MobSpawner;
use SkyBlock\tiles\OreGenTile;
use SkyBlock\util\Values;

class FormFunctions {

    /** @var Main */
    private Main $pl;
    /** @var ShopFormFunctions */
    private ShopFormFunctions $shop;
    /** @var CasinoFormFunctions */
    private CasinoFormFunctions $casino;
    /**@var ICFormFunctions */
    private ICFormFunctions $itemcloud;

    /**
     * FormFunctions constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        $this->pl = $plugin;
        $this->shop = new ShopFormFunctions($plugin, $this);
        $this->casino = new CasinoFormFunctions($plugin, $this);
        $this->itemcloud = new ICFormFunctions($plugin, $this);
    }

    /**
     * @return CasinoFormFunctions
     */
    public function getCasino() : CasinoFormFunctions {
        return $this->casino;
    }

    /**
     * @return ShopFormFunctions
     */
    public function getShop() : ShopFormFunctions {
        return $this->shop;
    }

    /**
     * @return ICFormFunctions
     */
    public function getItemCloud() : ICFormFunctions {
        return $this->itemcloud;
    }

    /**
     * @param Player $player
     */
    public function sendPrefMenu(Player $player) : void {
        $form = new CustomForm(null);
        $form->setTitle("Preferences");
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $pref = $user->getPref();

        $form->addToggle("Enable Server Capes", $pref->capes_enabled);
        $form->addToggle("Broadcast Join Msg", $pref->welcome_msg);
        $form->addToggle("Scoreboard", $pref->scoreboard_enabled);
        $form->addToggle("Enable Chairs", $pref->chair_feature);
        $form->addToggle("Form Button Size", $pref->button_size);
        $form->addToggle("Show //sc and /cc Messages", $pref->exclcmdmessages);
        //		$form->addToggle("Use Chest UI", $pref->chest_ui);
        $form->addDropdown("CE Activation", ["None", "Popup", "Message"], $pref->ce_act_type);
        $form->addLabel("Chat Configuration");
        $form->addToggle("Show Island Level", $pref->showIslandLevel);
        $form->addToggle("Show Island Rank", $pref->showIslandRank);
        $form->addToggle("Show Island Name", $pref->showIslandName);
        $form->addToggle("Show Player Ranks", $pref->showRanks);
        $form->addToggle("Show Player OS", $pref->showOS);
        $form->addToggle("Show Tags", $pref->showTags);
        $form->addToggle("Show Gangs", $pref->showGangs);
        $form->addToggle("Save Last Logout Position", $pref->saveLastPosition);
        //		$form->addToggle("Use Compact Chat", $pref->useCompactChat);


        $func = function(Player $player, ?array $data) use ($user) : void {
            if ($data === null) return;

            $user->getPref()->capes_enabled = $data[0];
            if ($data[0]) {
                $this->pl->getEvFunctions()->checkCape($player);
            } else {
                EvFunctions::addCape($player, 'none');
            }
            $user->getPref()->welcome_msg = $data[1];
            $user->getPref()->scoreboard_enabled = $data[2];
            $user->getPref()->chair_feature = $data[3];
            $user->getPref()->button_size = $data[4];
            $user->getPref()->exclcmdmessages = $data[5];
            //			$user->getPref()->chest_ui = $data[5];
            $user->getPref()->ce_act_type = $data[6];
            $user->getPref()->showIslandLevel = $data[8];
            $user->getPref()->showIslandRank = $data[9];
            $user->getPref()->showIslandName = $data[10];
            $user->getPref()->showRanks = $data[11];
            $user->getPref()->showOS = $data[12];
            $user->getPref()->showTags = $data[13];
            $user->getPref()->showGangs = $data[14];
            $user->getPref()->saveLastPosition = $data[15];
            $user->getPref()->chest_ui = false;

            $player->sendMessage(TextFormat::GREEN . "Server Preferences saved!");
        };
        $form->setCallable($func);
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     */
    public function sendProfileMenu(Player $player) : void {
        $form = new CustomForm(null);
        $form->setTitle("§e§lProfile viewer");
        $form->addInput("Enter a player's name to view their profile -", "Username");
        $form->setCallable(function(Player $player, ?array $data) : void {
            if ($data !== null) {
                $name = (string) $data[0];
                $namesake = $this->pl->getServer()->getOfflinePlayer(strtolower($name));
                if (!is_int((int) $namesake->getFirstPlayed())) {
                    $result = "Player not found! Enter the name properly";
                    $this->sendResultForm($player, $result, "sendProfileMenu");
                    return;
                }
                $this->sendProfileView($player, $name);
            }
        }
        );
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @param string $msg
     * @param string $funcName
     * @param array  $args
     */
    public function sendResultForm(Player $player, string $msg, string $funcName, array $args = []) : void {
        $this->sendModalForm($player, "§6Result", $msg, ["§2Go back", "§cExit"], function(Player $player, ?bool $data) use ($funcName, $args) {
            if ($data) {
                assert(method_exists($this, $funcName));
                array_unshift($args, $player);
                call_user_func_array([$this, $funcName], $args);
            }
        }
        );
    }

    /**
     * @param Player        $player
     * @param string        $title
     * @param string        $content
     * @param array         $buttons
     * @param callable|null $func
     */
    public function sendModalForm(Player $player, string $title, string $content, array $buttons, ?callable $func) : void {
        $form = new ModalForm($func);
        $form->setTitle($title);
        $form->setContent($content);
        $form->setButton1($buttons[0] ?? "Yes");
        $form->setButton2($buttons[1] ?? "No");
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     */
    public function sendTradeList(Player $player) : void {
        $buttons = $otrade = [];
        foreach ($this->pl->trades as $tid => $trade) {
            //            if (strtolower($player->getName()) !== $trade['trader'] && $this->pl->getServer()->getPlayerExact($trade['trader']) !== null) {
            //                if ($trade['type'] === 'item') {
            //                    $buttons[] = $trade['trader'] . "\n" . "§7x§c" . $trade['count'] . " §a" . $trade['name'];
            //                } else {
            //                    $buttons[] = $trade['trader'] . "\n" . "§6" . $trade['value'] . " §e" . $trade['type'];
            //                }
            //                $otrade[] = [$tid, $trade];
            //            }
            if (strtolower($player->getName() !== $trade['trader'] && $this->pl->getServer()->getPlayerExact($trade['trader']) !== null)) {
                if ($trade['type'] === 'item') {
                    $buttons[] = $trade['name'];
                }
                $otrade[] = [$tid, $trade];
            }
        }
        $func = function(Player $player, ?int $data) use ($otrade) : void {
            if ($data === null) return;
            $this->sendTradeInfo($player, $otrade[$data][0]);
        };
        $this->sendSimpleForm($player, "§d§lOnline Trades", "§fSelect a trade for info -", $buttons, $func);
    }

    public function sendTradeInfo(Player $player, int $tid, bool $sendOffer = true) : void {
        if (!isset($this->pl->trades[$tid])) {
            $player->sendMessage(TF::RED . "Trade doesnt exist anymore!");
            return;
        }
        $trade = $this->pl->trades[$tid];
        if ($this->pl->getServer()->getPlayerExact($trade['trader']) === null) {
            $player->sendMessage(TF::RED . "Trader not online anymore!");
            return;
        }
        $toSend = TF::AQUA . 'Trader: ' . TF::GREEN . $trade['trader'] . "\n";
        if ($trade['type'] === 'item')
            $toSend .= Trade::itemInfoString(Item::nbtDeserialize($trade['item']));
        else
            $toSend .= TF::AQUA . "Trade: §f" . ucfirst($trade['type']) . "\n" . TF::AQUA . "Value: §f" . number_format($trade['value']);
        if ($sendOffer) {
            $func = function(Player $player, ?bool $data) use ($tid) : void {
                if ($data === null) return;
                if ($data) {
                    $this->sendTradeOffer($player, $tid);
                } else {
                    $this->sendTradeList($player);
                }
            };
            $this->sendModalForm($player, "§b" . ucfirst($trade['trader']) . "'s Trade", $toSend, ['Send Offer', 'Go Back'], $func);
        } else {
            $this->sendModalForm($player, "§b" . "Your Trade", $toSend, ['Exit', 'Exit'], null);
        }
    }

    public function sendTradeOffer(Player $player, int $tid) : void {
        if (!isset($this->pl->trades[$tid])) {
            $player->sendMessage(TF::RED . "Trade doesnt exist anymore!");
            return;
        }
        $trade = $this->pl->trades[$tid];
        if ($this->pl->getServer()->getPlayerExact($trade['trader']) === null) {
            $player->sendMessage(TF::RED . "Trader not online anymore!");
            return;
        }
        $form = new CustomForm(null);
        $form->setTitle("§bSend your Offer");
        $opts = ["Held Item", "Money", "Mana", "MobCoin"];
        $form->addDropdown("Offer type", $opts);
        $form->addInput("Offer value", "", "1000");
        $form->setCallable(function(Player $player, ?array $data) use ($tid, $opts) : void {
            if ($data === null) return;
            if (!isset($this->pl->trades[$tid])) {
                $player->sendMessage(TF::RED . "Trade doesnt exist anymore!");
                return;
            }
            $trade = $this->pl->trades[$tid];
            if (($trader = $this->pl->getServer()->getPlayerExact($trade['trader'])) === null) {
                $player->sendMessage(TF::RED . "Trader not online anymore!");
                return;
            }
            if (!isset($opts[$data[0]])) return;
            if (!is_int((int) $data[1]) or $data[1] < 1) {
                $player->sendMessage(TF::RED . "Error: Enter offer value greater than 1!");
                return;
            }
            $data[1] = (int) $data[1];
            if (isset($this->pl->trade_offers[$tid])) {
                foreach ($this->pl->trade_offers[$tid] as $offer) {
                    if ($offer["trader"] === $player->getName()) {
                        $player->sendMessage(TF::RED . "Error: You have already sent Trade offer to that trader. Please ask them to respond via /trade offers");
                        return;
                    }
                }
            }
            $otype = $opts[$data[0]];
            $ovalue = (int) $data[1];
            $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
            if ($otype !== "Held Item") {
                if (!Trade::checkTradeRequirements($user, $otype, $ovalue)) {
                    $this->sendMessage($player, "§4[Error]§c You don't have §6" . number_format($ovalue) . "§cof §6{$otype} §cto send that trade offer!");
                    return;
                }
                $this->pl->trade_offers[$tid][] = [
                    "trader" => $player->getName(),
                    "type"   => $otype,
                    "value"  => $ovalue
                ];
            } else {
                if (!Trade::checkTradeRequirements($user, $otype, $ovalue)) {
                    $this->sendMessage($player, "§4[Error]§c Item in hand not valid for trade offer.");
                    return;
                }
                $this->pl->trade_offers[$tid][] = [
                    "trader" => $player->getName(),
                    "type"   => $otype,
                    "item"   => $player->getInventory()->getItemInHand()
                ];
                $this->sendMessage($player, "§c§lPlease keep the same held item until the trade offer is responded by the trader or the trade wont happen!");
            }
            $this->sendMessage($player, "§eYou have sent a §b`{$otype}` §etrade offer to `§a{$trader->getName()}§e`!");
            $this->sendMessage($trader, "§eYou have received a §b`{$otype}` §etrade offer from `§a{$player->getName()}§e`!\n§fUse /trade offers to see all offers.");
        }
        );
        $player->sendForm($form);
    }

    public function sendTradeOffers(Player $player, int $tid) : void {
        $buttons = $otrade = [];
        foreach ($this->pl->trade_offers[$tid] as $trid => $trade) {
            $buttons[] = $trade['trader'] . "\n§a" . $trade['type'];
            $otrade[] = [$trid, $trade];
        }
        $func = function(Player $player, ?int $data) use ($tid, $otrade) : void {
            if ($data === null) return;
            $this->sendTradeOfferInfo($player, $tid, $otrade[$data][0]);
        };
        $this->sendSimpleForm($player, "§bTrade Offers", "§fSelect a trade offer for info -", $buttons, $func);
    }

    public function sendTradeOfferInfo(Player $player, int $tid, int $trid) : void {
        if (!isset($this->pl->trade_offers[$tid][$trid])) {
            $this->sendMessage($player, "§4[Error]§c That Trade offer doesnt exist anymore!");
            return;
        }
        $trade_offer = $this->pl->trade_offers[$tid][$trid];
        if ($this->pl->getServer()->getPlayerExact($trade_offer['trader']) === null) {
            $this->sendMessage($player, "§4[Error]§c That Trader is now offline!");
            return;
        }
        $cont = "§fTrade Offer Info - \n";
        $cont .= TF::AQUA . 'Offer by: ' . TF::WHITE . $trade_offer['trader'] . "\n";
        if ($trade_offer['type'] === 'Held Item') {
            $cont .= Trade::itemInfoString($trade_offer['item']);
        } else {
            $cont .= "§bType - §f" . $trade_offer['type'] . "\n" . "§bValue - §f" . $trade_offer['value'];
        }
        $func = function(Player $player, ?bool $data) use ($tid, $trid) : void {
            if ($data === null) return;
            if (!isset($this->pl->trade_offers[$tid][$trid])) {
                $this->sendMessage($player, "§4[Error]§c That Trade offer doesnt exist anymore!");
                return;
            }
            $trade_offer = $this->pl->trade_offers[$tid][$trid];
            if (($trader = $this->pl->getServer()->getPlayerExact($trade_offer['trader'])) === null) {
                $this->sendMessage($player, "§4[Error]§c That Trader is now offline!");
                return;
            }
            if ($data) {
                $user = $this->pl->getUserManager()->getOnlineUser($trader->getName());
                if ($trade_offer['type'] !== "Held Item") {
                    if (!Trade::checkTradeRequirements($user, $trade_offer['type'], $trade_offer['value'])) {
                        $this->sendMessage($trader, "§4[Error]§c You don't have §6" . number_format($trade_offer['value']) . "§cof §6{$trade_offer['type']} §cfor trade offer with §a`{$player->getName()}`.");
                        $this->sendMessage($player, "§4[Error]§c §a`{$trader->getName()}` doesn't have §6" . number_format($trade_offer['value']) . "§cof §6{$trade_offer['type']} §canymore!");
                        return;
                    }
                } else {
                    if (!Trade::checkTradeRequirements($user, $trade_offer['type'])) {
                        $this->sendMessage($trader, "§4[Error]§c Item in hand not valid for trade offer with §a`{$player->getName()}`.");
                        $this->sendMessage($player, "§4[Error]§c §a`{$trader->getName()}` §cis not holding a valid item anymore");
                        return;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($player)) {
                        $this->sendMessage($player, "§4[Error]§c Your inventory is full to accept that trade!");
                        return;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($trader)) {
                        $this->sendMessage($trader, "§4[Error]§c Your inventory is full to get §a`{$player->getName()}` §ctrade!");
                        $this->sendMessage($player, "§4[Error]§c §a`{$trader->getName()}`§c's inventory is full to accept trade!");
                        return;
                    }
                    if (!Functions::itemEquals($trader->getInventory()->getItemInHand(), $trade_offer['item'])) {
                        $this->sendMessage($trader, "§4[Error]§c Item in hand is not the same as your trade offer sent to §a`{$player->getName()}`.");
                        $this->sendMessage($player, "§4[Error]§c §a`{$trader->getName()}` §cis not holding that same item anymore");
                        return;
                    }
                }
                $trade = $this->pl->trades[$tid];
                $user2 = $this->pl->getUserManager()->getOnlineUser($player->getName());
                switch ($trade_offer['type']) {
                    case "Money":
                        $user2->addMoney($trade_offer['value'], false);
                        $user->removeMoney($trade_offer['value']);
                        break;
                    case "Mana":
                        $user2->addMana($trade_offer['value'], false);
                        $user->removeMana($trade_offer['value']);
                        break;
                    case "MobCoin":
                        $user2->addMobCoin($trade_offer['value'], false);
                        $user->removeMobCoin($trade_offer['value']);
                        break;
                    case "Held Item":
                        $player->getInventory()->addItem($trader->getInventory()->getItemInHand());
                        $trader->getInventory()->setItemInHand(VanillaItems::AIR());
                        break;
                }
                switch ($trade['type']) {
                    case "money":
                        $user->addMoney($trade['value'], false);
                        break;
                    case "mana":
                        $user->addMana($trade['value'], false);
                        break;
                    case "mobcoin":
                        $user->addMobCoin($trade['value'], false);
                        break;
                    case "item":
                        $trader->getInventory()->addItem(Item::nbtDeserialize($trade['item']));
                        break;
                }
                unset($this->pl->trades[$tid]);
                $this->sendMessage($trader, "§eTrading with §a`{$player->getName()}` §ewas successful.");
                $this->sendMessage($player, "§eTrading with §a`{$trader->getName()}` §ewas successful.");
            } else {
                $this->sendMessage($player, "§cDenied §a`{$trader->getName()}`'s §ctrade offer successfully!");
                $this->sendMessage($trader, "§a`{$player->getName()}` §cdenied your trade offer!");
            }
            unset($this->pl->trade_offers[$tid][$trid]);
        };
        $this->sendModalForm($player, "§bTrade Response", $cont, ["Accept", "Deny"], $func);
    }

    /**
     * @param Player $player
     * @param string $name
     */
    public function sendProfileView(Player $player, string $name) : void {
        $name2 = ucfirst($name);
        $form = new CustomForm(null);
        $form->setTitle("§b§l" . ucfirst($name) . "'s Profile");
        $player2 = $this->pl->getServer()->getOfflinePlayer(strtolower($name));
        $rank = $this->pl->permsapi->getUserGroup($name)->getName();
        $online = false;
        if (($user = $this->pl->getUserManager()->getOnlineUser($name)) !== null) {
            $online = true;
            $money = $user->getMoney();
            $won = $user->getWon();
            $kills = $user->getKills();
            $deaths = $user->getDeaths();
            $mana = $user->getMana();
            $mc = $user->getMobCoin();
            $xpbank = $user->getXPBank();
            $xp = $user->getXP();
            $gang = $user->getGang();
            $blocks = $user->getBlocksBroken();
            $timeplayed = $user->getTimePlayed();
            if ($user->isIslandSet()) {
                $island = $user->getIsland();
                if ($user->hasIsland()) $irank = 'Owner';
                else $irank = 'CoOwner';
            } else $island = '-';
        } else {
            $data = $this->pl->getDb()->getPlayerValues($name);
            if (is_bool($data)) {
                $this->sendMessage($player, "Player not found!");
                return;
            }
            $money = $data['money'];
            $xp = $data['xp'];
            $xpbank = $data['xpbank'];
            $mana = $data['mana'];
            $mc = $data['mobcoin'];
            $won = $data['won'];
            $kills = $data['kills'];
            $deaths = $data['deaths'];
            $blocks = $data['blocks'];
            $gang = $this->pl->getDb()->getPlayerGang($name) ?? "-";
            $timeplayed = $this->pl->getDb()->getTimePlayed($name);
            $island = $this->pl->getUserManager()->getPlayerIsland($name) ?? "-";
            if ($island !== "-") $irank = $this->pl->getDb()->getPlayerIslandRank($name, $island);
        }
        $date = "";
        $time = "";
        if (!$online) {
            $date = date("l, F j, Y", (int) ($last = $player2->getLastPlayed() / 1000));
            $time = date("h:ia", (int) $last);
        }
        if ($kills > 0 && $deaths > 0) {
            $kdr = round($kills / $deaths);
        } else {
            $kdr = 'N/A';
        }
        $form->addLabel("§6Name: §f" . $name2);
        $form->addLabel("§6Rank: §f" . $rank);
        $form->addLabel("§6Money: §f" . number_format($money) . "$");
        $form->addLabel("§6Mana: §f" . number_format($mana));
        $form->addLabel("§6Mobcoin: §f" . number_format($mc));
        $form->addLabel("§6XP: §f" . number_format($xp));
        $form->addLabel("§6XPBank: §f" . number_format($xpbank));
        $form->addLabel("§6Blocks Broken: §f" . number_format($blocks));
        $form->addLabel("§6Casinos Won: §f" . $won);
        $form->addLabel("§6Time Played: §f" . $timeplayed);
        $form->addLabel("§6Island: §f" . $island);
        $form->addLabel("§6Gang: §f" . $gang);
        if (isset($irank)) $form->addLabel("§6Island Rank: §f" . $irank);
        $form->addLabel("§6Kills: §f" . $kills);
        $form->addLabel("§6Deaths: §f" . $deaths);
        $form->addLabel("§6KDR: §f" . $kdr);
        if ($online) {
            $form->addLabel("§6Status: §a" . "ONLINE");
        } else {
            $form->addLabel("§6Status: §c" . "OFFLINE");
            $form->addLabel("§6Last Seen: §f" . "$date at $time");
        }
        $form->setCallable(function(Player $player, ?array $data) : void {
            if ($data !== null) {
                $this->sendProfileMenu($player);
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendMatchConfirm(Player $player) {
        $func = function(Player $player, ?bool $data) : void {
            if ($data) {
                if ($player->getWorld()->getDisplayName() === "PvP") {
                    $player->sendMessage("§cCommand not usable from here!");
                    return;
                }
                $plugin = $this->pl->getServer()->getPluginManager()->getPlugin('1vs1');
                // @phpstan-ignore-next-line
                $plugin->getInstance()->arenaManager->addNewPlayerToQueue($player);
            }
        };
        $this->sendModalForm($player, "§c§lMatch Confirmation:", "Match will §cclear your inventory, §fonly accept if you have stored your inventory somewhere or else it will be §cgone forever §fand §ewe wont be held responsible.", ["Join Queue", "Exit"], $func);
    }

    /**
     * @param Player $player
     * @param string $enchant
     */
    public function sendCEEnchantInfo(Player $player, string $enchant) : void {
        $form = new CustomForm(null);
        $form->setTitle("§e§l$enchant §8CE Info");
        $data = $this->pl->getEnchantFactory()->getEnchantmentByName($enchant);
        if ($data === null) {
            return;
        }
        $type = $data[4];
        $id = $this->pl->getEnchantFactory()->getIdByEnchantName($enchant);
        $form->addLabel("§aCE Name - §f$enchant");
        if ($this->pl->isVaulted($id)) $form->addLabel("§aVaulted - §cYes");
        else $form->addLabel("§4Vaulted - §eNo");
        $form->addLabel("§eRarity - §f$data[3]");
        $form->addLabel("§eType - §f$type");
        $form->addLabel("§eInfo -");
        $form->addLabel("§f$data[5]");
        $form->setCallable(function(Player $player, ?array $data) use ($type) : void {
            if ($data !== null) {
                $this->sendCETypeMenu($player, $type);
            }
        }
        );
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @param string $type
     */
    public function sendCETypeMenu(Player $player, string $type) : void {
        $buttons = $enchants = ["Back"];
        foreach ($this->pl->getEnchantFactory()->getTypeEnchants($type) as $enchant) {
            $id = $this->pl->getEnchantFactory()->getIdByEnchantName($enchant);
            if ($this->pl->isVaulted($id)) $buttons[] = $enchant . "§c[Vaulted]";
            else   $buttons[] = $enchant;
            $enchants[] = $enchant;
        }
        $func = function(Player $player, ?int $data) use ($enchants) : void {
            if ($data !== null) {
                if (isset($enchants[$data])) {
                    if ($data == 0) {
                        $this->sendCEInfoMenu($player);
                        return;
                    } else $enchant = $enchants[$data];
                } else {
                    return;
                }
                $this->sendCEEnchantInfo($player, $enchant);
            }
        };
        $this->sendSimpleForm($player, "§aCE List", "$type Type CEs -", $buttons, $func);
    }

    /**
     * @param Player $player
     */
    public function sendCEInfoMenu(Player $player) : void {
        $buttons = $types = [];
        foreach ($this->pl->getEnchantFactory()->getUniqueTypes() as $type) {
            $type = ucfirst($type);
            $buttons[] = $types[] = $type;
        }
        $func = function(Player $player, ?int $data) use ($types) : void {
            if ($data !== null) {
                if (isset($types[$data])) {
                    $this->sendCETypeMenu($player, $types[$data]);
                }
            }
        };
        $this->sendSimpleForm($player, "§e§lCE List", "§6Select a type of CE -", $buttons, $func);
    }

    /**
     * @param Player        $player
     * @param string        $title
     * @param string        $content
     * @param array         $buttons
     * @param callable|null $func
     */
    public function sendSimpleForm(Player $player, string $title, string $content, array $buttons, ?callable $func) : void {
        $form = new SimpleForm($func);
        $form->setTitle($title);
        $form->setContent($content);
        foreach ($buttons as $btn) $form->addButton($btn);
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     */
    public function sendGoalsLevelMenu(Player $player) : void {
        $buttons = [];
        $maxlevel = $this->pl->getGoalManager()->maxlevel;
        for ($i = 1; $i <= $maxlevel; $i++) {
            $data = $this->pl->getGoalManager()->getUserGoalLevelData($player, $i);
            $buttons[] = "§fLevel $i\n§r{$data[0]}/{$data[1]}";
            if (!$data[2]) break;
            if ($maxlevel == $i) break;
        }
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                $this->sendGoalsMainMenu($player, ++$data);
            }
        };
        $this->sendSimpleForm($player, "§6Goals", "§fSelect a Goal Level -", $buttons, $func);
    }

    /**
     * @param Player $player
     * @param int    $level
     */
    public function sendGoalsMainMenu(Player $player, int $level) : void {
        $i = 1;
        $buttons = ["Back"];
        $goals[0] = 0;
        foreach ($this->pl->getGoalManager()->getGoalsByLevel($level) as $id => $data) {
            if ($this->pl->getGoalManager()->isGoalCompleted($player, $id)) $status = "§aCompleted";
            else $status = "§cIncomplete";
            $buttons[] = "§fGoal $i\n §f- $status §f- ";
            $goals[] = $id;
            $i++;
        }
        $data = $this->pl->getGoalManager()->getUserGoalLevelData($player, $level);
        $func = function(Player $player, ?int $data) use ($goals, $level) : void {
            if ($data !== null) {
                if ($data == 0) {
                    $this->sendGoalsLevelMenu($player);
                    return;
                }
                if (isset($goals[$data])) $goal = [$data, $goals[$data]];
                else return;
                $this->sendGoalsInfoMenu($player, $goal, $level);
            }
        };
        $this->sendSimpleForm($player, "Level $level Goals: {$data[0]}/{$data[1]}", "§6Select a Goal to check -", $buttons, $func);
    }

    public function sendGoalsInfoMenu(Player $player, array $data, $level) {
        $form = new CustomForm(null);
        $form->setTitle("Goal Info -");
        $number = $data[0];
        $goal = $data[1];
        $data = $this->pl->getGoalManager()->getGoalData($goal);
        $form->addLabel("§eLevel - §f{$data['level']}");
        $form->addLabel("§eGoal no - §f{$number}");
        $form->addLabel("§eGoal Info - §f{$data['content']}");
        if (!$this->pl->getGoalManager()->isGoalCompleted($player, $goal)) {
            $status = "§cIncomplete";
            $form->addLabel("§eProgress - §f{$this->pl->getGoalManager()->progress($player, $goal)}§7/§f{$data['count']}");
        } else   $status = "§aCompleted";
        $form->addLabel("§eStatus - §7`§f{$status}§7`");
        $form->addLabel("§eReward - §b{$data['mana']} Mana");
        $form->addLabel("§7Reward will be given automatically.");
        $form->setCallable(function(Player $player, ?array $data) use ($level) : void {
            if ($data !== null) {
                $this->sendGoalsMainMenu($player, $level);
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendIslandPermsMain(Player $player) : void {
        $members = $buttons = [];
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        if (($island = $this->pl->getIslandManager()->getOnlineIsland($user->getIsland())) === null) return;
        foreach ($island->getHelpers() as $name) {
            $name = ucfirst($name);
            $lower = strtolower($name);
            $buttons[] = $name;
            $members[] = $lower;
        }
        $func = function(Player $player, ?int $data) use ($island, $members) : void {
            if ($data !== null) {
                if (isset($members[$data])) {
                    $member = $members[$data];
                    $this->sendIslandPermsSelect($player, $island, $member);
                } else $this->sendIslandPermsMain($player);
            }
        };
        $this->sendSimpleForm($player, "§f§lPermissions", "§6Select an Island member -", $buttons, $func);
    }

    public function sendIslandPermsSelect(Player $player, Island $island, string $member) : void {
        $form = new CustomForm(null);
        $form->setTitle("§f§lPermissions");
        $form->addLabel("§fMember - §a" . $member);
        foreach (PermissionManager::getPermissions() as $pname => $perm) {
            $form->addToggle(ucfirst($pname) . "\n§7[" . $perm->getDesc() . "]", $island->hasPerm($member, $pname));
        }
        $form->setCallable(function(Player $player, ?array $data) use ($member) : void {
            if ($data === null) return;
            $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
            if (!$user->hasIsland()) {
                $result = "§4[Error] §cYou arent Owner of the Island to change member perms!";
                $this->sendResultForm($player, $result, "sendIslandPermsMain");
                return;
            }
            if (($island = $this->pl->getIslandManager()->getOnlineIsland($user->getIsland())) === null) {
                $result = "§4[Error] §cIsland not online!";
                $this->sendResultForm($player, $result, "sendIslandPermsMain");
                return;
            }
            if (!$island->isHelper($member)) {
                $result = "§4[Error] §cSelected Member §a" . $member . " §cis not the member of the Island anymore!";
                $this->sendResultForm($player, $result, "sendIslandPermsMain");
                return;
            }
            $i = 1;
            foreach (PermissionManager::getPermissions() as $perm) {
                $island->setPerm($member, $perm->getName(), $data[$i++]);
            }
            $result = "§a" . $member . "'s §bperms §fpermissions were successfully changed!";
            $this->sendResultForm($player, $result, "sendIslandPermsMain");
        }
        );
        $player->sendForm($form);
    }

    public function sendAutoMinerFortuneUpgrade(Player $player, Block $block) : void {
        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
        if (!$tile instanceof AutoMinerTile) return;
        $level = $tile->level1;
        $flevel = $tile->flevel;
        $percost = 3500;
        $details = "§eCurrent Level - §7$level\n§eCurrent Fortune Level - §7$flevel\n§eCurrent Mining Delay - §7{$tile->getDelayInSeconds()}s\n";
        if ($level >= AutoMinerTile::MAX_LEVEL && $flevel >= AutoMinerTile::MAX_FORTUNE_LEVEL) {
            $player->sendMessage("§cError: That Auto Miner is already on max level with Fortune" . "\n§bDetails: $details");
            return;
        }

        $form = new CustomForm(null);
        $form->setTitle("Upgrade Auto Miner w/ Fortune");
        $form->addLabel("$details\n§ePerks:\n- §aLess Mining Delay");
        $ranges = range($level, AutoMinerTile::MAX_LEVEL);
        $form->addDropdown("Select level", array_map('strval', $ranges));
        $franges = range($flevel, AutoMinerTile::MAX_FORTUNE_LEVEL);
        $form->addDropdown("Select Fortune level", array_map('strval', $franges));

        $func = function(Player $player, ?array $data) use ($block, $ranges, $franges, $percost) : void {
            if ($data !== null and isset($ranges[$data[1]]) and isset($franges[$data[2]])) {
                if (($world = $block->getPosition()->getWorld()) === null) return;
                $tile = $world->getTile($block->getPosition());
                if (!$tile instanceof AutoMinerTile) return;
                $level = $tile->level1;
                $flevel = $tile->flevel;
                if ($level >= AutoMinerTile::MAX_LEVEL && $flevel >= AutoMinerTile::MAX_FORTUNE_LEVEL) {
                    $player->sendMessage("§cThat Auto Miner is already on max level with Fortune");
                    return;
                }

                $new_level = $ranges[$data[1]];
                $new_flevel = $franges[$data[2]];
                if ($new_level === $level && $new_flevel === $flevel) {
                    $player->sendMessage("§cYou didnt select any new levels to upgrade to.");
                    return;
                }
                if (($cost = $this->getUpgradeCost($level, $new_level, $percost)) === null) $cost = 0;
                if (($fcost = $this->getUpgradeCost($flevel, $new_flevel, $percost)) === null) $fcost = 0;
                $tcost = $cost + $fcost;
                if ($tcost === 0) {
                    $player->sendMessage("§cYou didnt select any new levels to upgrade to.");
                    return;
                }
                $content = "§fAre you sure you want to upgrade this §aAutoMiner w/ Fortune?
                \n§eAutominer Level upgrade §7{$level} §a-> §7{$new_level}
                \n§eFortune Level upgrade §7{$flevel} §a-> §7{$new_flevel}
                \n§eCost calculated -> §a" . number_format($cost) . " + " . number_format($fcost) . " = " . number_format($tcost) . " mana";
                $bfunc = function(Player $player, ?bool $data) use ($block, $new_level, $new_flevel, $tcost) : void {
                    if ($data) {
                        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                        if (!$tile instanceof AutoMinerTile) return;
                        $level = $tile->level1;
                        $flevel = $tile->flevel;
                        if ($level >= AutoMinerTile::MAX_LEVEL && $flevel >= AutoMinerTile::MAX_FORTUNE_LEVEL) {
                            $player->sendMessage("§cThat Auto Miner is already on max level with Fortune");
                            return;
                        }

                        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                        if (!$user->hasMana($tcost)) {
                            $player->sendMessage("§cYou dont have enough mana to upgrade Autominer to $new_level and Fortune to $new_flevel! §eRequired - §6" . number_format($tcost) . " mana");
                        } else {
                            $user->removeMana($tcost);
                            $tile->level1 = $new_level;
                            $tile->flevel = $new_flevel;
                            $newsecs = (int) $tile->getDelayByLevel($new_level) / 20;
                            $tile->setDelay($newsecs);
                            $player->sendMessage("§aSuccessfully upgraded Autominer w/ Fortune to $new_level and Fortune to $new_flevel. §aUsed §6" . number_format($tcost) . " mana");
                        }
                    }
                };
                $this->sendModalForm($player, "Confirm Upgrade", $content, [], $bfunc);
            }
        };
        $form->setCallable($func);
        $player->sendForm($form);
    }

    public function sendAutoMinerUpgrade(Player $player, Block $block) : void {
        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
        if (!$tile instanceof AutoMinerTile) return;
        $level = $tile->level1;
        $details = "§eCurrent Level - §7$level\n§eCurrent Mining Delay - §7{$tile->getDelayInSeconds()}s\n";
        //        if ($level >= AutoMinerTile::MAX_LEVEL) {
        //            $player->sendMessage("§cError: That Auto Miner is already on max level " . AutoMinerTile::MAX_LEVEL . "\n§bDetails: $details");
        //            return;
        //        }
        $percost = 3500;

        $form = new CustomForm(null);
        $form->setTitle("Upgrade Auto Miner");
        $form->addLabel("$details\n§ePerks:\n- §aLess Mining Delay");
        $ranges = range($level, AutoMinerTile::MAX_LEVEL);
        if ($level < AutoMinerTile::MAX_LEVEL) {
            $form->addDropdown("Select level", array_map('strval', $ranges), label: "level");
        } else {
            $form->addDropdown("Select level", [strval(AutoMinerTile::MAX_LEVEL)], label: "level");
        }

        $fortune = range($tile->fortune, AutoMinerTile::MAX_FORTUNE_LEVEL);
        if ($tile->fortune < AutoMinerTile::MAX_FORTUNE_LEVEL) {
            $form->addDropdown("Fortune: Fortune upgrade for the autominer (does not work on ancient debris)", array_map('strval', $fortune), label: "fortune");
        } else {
            $form->addDropdown("Fortune: Fortune upgrade for the autominer (does not work on ancient debris)", [strval(AutoMinerTile::MAX_FORTUNE_LEVEL)], label: "fortune");
        }

        $func = function(Player $player, ?array $data) use ($block, $ranges, $fortune, $percost) : void {
            $bfunc = null;  // Define $bfunc initially
            $content = "";  // Define $content initially
            if ($data == null) {
                echo("null data\n");
                return;
            }
            if (($data["level"] > 0 or isset($ranges[$data["level"]])) or ($data["fortune"] > 0 or isset($fortune[$data["fortune"]]))) {
                $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                if (!$tile instanceof AutoMinerTile) return;
                $level = $tile->level1;
                $flevel = $tile->fortune;
                if ($level >= AutoMinerTile::MAX_LEVEL and $flevel >= AutoMinerTile::MAX_FORTUNE_LEVEL) {
                    $player->sendMessage("§cThat Auto Miner is already on max level " . AutoMinerTile::MAX_LEVEL);
                    return;
                }

                $new_level = $ranges[$data["level"]];
                $new_fortune_level = $fortune[$data["fortune"]];
                $cost = 0;
                if (($cost += $this->getUpgradeCost($level + 1, $new_level + 1, $percost)) === null) return;
                if (($cost += $this->getUpgradeCost($flevel + 1, $new_fortune_level + 1, 6500)) === null) return;
                if ($new_level == 0 or $cost == 0) return;
                $content = "§fAre you sure you want to upgrade this §aAutoMiner?
                \n§eAutominer Level upgrade §7{$level} §a-> §7{$new_level}
                \n§eAutominer Fortune upgrade §7{$flevel} §a-> §7{$new_fortune_level}
                \n§eCost calculated -> §a" . number_format($cost) . " mana";
                $bfunc = function(Player $player, ?bool $data) use ($block, $new_level, $new_fortune_level, $cost) : void {
                    if ($data) {
                        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                        if (!$tile instanceof AutoMinerTile) return;
                        $level = $tile->level1;
                        $flevel = $tile->flevel;
                        if ($level >= AutoMinerTile::MAX_LEVEL and $flevel >= AutoMinerTile::MAX_FORTUNE_LEVEL) {
                            $player->sendMessage("§cThat Auto Miner is already on max level " . AutoMinerTile::MAX_LEVEL);
                            return;
                        }
                        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                        if (!$user->hasMana($cost)) {
                            $player->sendMessage("§cYou dont have enough mana to upgrade to §a$new_level! §eRequired - §6" . number_format($cost) . " mana");
                        } else {
                            $user->removeMana($cost);
                            $tile->level1 = $new_level;
                            $tile->fortune = $new_fortune_level;
                            $newsecs = (int) $tile->getDelayByLevel($new_level) / 20;
                            $tile->setDelay($newsecs);
                            $player->sendMessage("§aSuccessfully upgraded AutoMiner to level §e$level -> $new_level. §aUsed §6" . number_format($cost) . " mana");
                        }
                    }
                };
            }
            $this->sendModalForm($player, "Confirm Upgrade", $content, [], $bfunc);
        };
        $form->setCallable($func);
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     * @param Block  $block
     */
    public function sendAutoSellerUpgrade(Player $player, Block $block) : void {
        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
        if (!$tile instanceof AutoSellerTile) return;
        $level = $tile->level1;
        $details = "§eCurrent Level - §7$level\n§eType - " . AutoSellerTile::getTypeName($tile->type) . "\n§7$level §eitem sold together\n§eCurrent Selling Delay - §7{$tile->getDelayInSeconds()}s";
        if ($level >= AutoSellerTile::MAX_LEVEL) {
            $player->sendMessage("§cError: That Auto Seller is already on max level " . AutoSellerTile::MAX_LEVEL . "\n§bDetails: $details");
            return;
        }
        $percost = 5000;

        $form = new CustomForm(null);
        $form->setTitle("Upgrade Auto Seller");
        $form->addLabel("$details\n§ePerks:\n- §aLess Mining Delay");
        $ranges = range($level + 1, AutoSellerTile::MAX_LEVEL);
        $form->addDropdown("Select level", array_map('strval', $ranges));

        $func = function(Player $player, ?array $data) use ($block, $ranges, $percost) : void {
            if ($data !== null and isset($ranges[$data[1]])) {
                $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                if (!$tile instanceof AutoSellerTile) return;
                $level = $tile->level1;
                if ($level >= AutoSellerTile::MAX_LEVEL) {
                    $player->sendMessage("§cThat Auto Seller is already on max level " . AutoSellerTile::MAX_LEVEL);
                    return;
                }

                $new_level = $ranges[$data[1]];
                if (($cost = $this->getUpgradeCost($level, $new_level, $percost)) === null) return;
                $content = "§fAre you sure you want to upgrade this §aAutSeller?
                \n§eAutoSeller Level upgrade §7{$level} §a-> §7{$new_level}
                \n§eCost calculated -> §a" . number_format($cost) . " mana";
                $bfunc = function(Player $player, ?bool $data) use ($block, $new_level, $cost) : void {
                    if ($data) {
                        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                        if (!$tile instanceof AutoSellerTile) return;
                        $level = $tile->level1;
                        if ($level >= AutoSellerTile::MAX_LEVEL) {
                            $player->sendMessage("§cThat Auto Seller is already on max level " . AutoSellerTile::MAX_LEVEL);
                            return;
                        }
                        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                        if (!$user->hasMana($cost)) {
                            $player->sendMessage("§cYou dont have enough mana to upgrade to §a$new_level! §eRequired - §6" . number_format($cost) . " mana");
                        } else {
                            $user->removeMana($cost);
                            $tile->level1 = $new_level;
                            $newsecs = (int) $tile->getDelayByLevel($new_level) / 20;
                            $tile->setDelay((int) $newsecs);
                            $player->sendMessage("§aSuccessfully upgraded Auto Seller to level §e$level -> $new_level. §aUsed §6" . number_format($cost) . " mana");
                        }
                    }
                };
                $this->sendModalForm($player, "Confirm Upgrade", $content, [], $bfunc);
            }
        };
        $form->setCallable($func);
        $player->sendForm($form);
    }

    /**
     * @param int $level
     * @param int $new_level
     * @param int $percost
     *
     * @return int|null
     */
    public function getUpgradeCost(int $level, int $new_level, int $percost) : ?int {
        $cost = 0;
        for ($i = $level; $i < $new_level; $i++) {
            $cost += $percost * $i;
            if ($i >= 4) {
                $cost += $percost * $i * 0.8;
            }
        }
        if ($cost === 0) return null;
        else return $cost;
    }

    public function sendOreGenUpgrade(Player $player, Block $block) : void {
        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
        if (!$tile instanceof OreGenTile) return;
        $level = $tile->level1;
        $details = "§eCurrent Level - §7$level\n§eCurrent Ore Delay - §7{$tile->getDelayInSeconds()}s";
        if ($level >= $tile->maxlevel) {
            $player->sendMessage("§cError: That Ore Gen is already on max level " . $tile->maxlevel . "\n§bDetails: $details");
            return;
        }
        $costarr = [BlockTypeIds::COAL_ORE => 750, BlockTypeIds::NETHERRACK => 500, BlockTypeIds::IRON_ORE => 1500, BlockTypeIds::LAPIS_LAZULI => 5000, BlockTypeIds::GOLD_ORE => 10000, BlockTypeIds::DIAMOND_ORE => 15000, BlockTypeIds::EMERALD_ORE => 30000, BlockTypeIds::NETHER_QUARTZ_ORE => 80000, BlockTypeIds::ANCIENT_DEBRIS => 150000];
        $percost = $costarr[$tile->oreid];
        //$buttons = ["1","2","3","4","5","6","7","8"];

        $form = new CustomForm(null);
        $form->setTitle("Upgrade Ore Gen");
        $form->addLabel("$details\n§ePerks:\n- §aLess Delay between ore spawn");
        $ranges = range($level + 1, $tile->maxlevel);
        $form->addDropdown("Select level", array_map('strval', $ranges));

        $func = function(Player $player, ?array $data) use ($block, $ranges, $percost) : void {
            if ($data !== null and isset($ranges[$data[1]])) {
                $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                if (!$tile instanceof OreGenTile) return;
                $level = $tile->level1;
                if ($level >= $tile->maxlevel) {
                    $player->sendMessage("§cThat Ore Gen is already on max level " . $tile->maxlevel);
                    return;
                }

                $new_level = $ranges[$data[1]];
                if (($cost = $this->getUpgradeCost($level, $new_level, $percost)) === null) return;

                $content = "§fAre you sure you want to upgrade this §aOreGen?
                \n§eOreGen Level upgrade §7{$level} §a-> §7{$new_level}
                \n§eCost calculated -> §a" . number_format($cost) . " mana";
                $bfunc = function(Player $player, ?bool $data) use ($block, $new_level, $cost) : void {
                    if ($data) {
                        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                        if (!$tile instanceof OreGenTile) return;
                        $level = $tile->level1;
                        if ($level >= OreGenTile::MAX_LEVEL) {
                            $player->sendMessage("§cThat Ore Gen is already on max level " . OreGenTile::MAX_LEVEL);
                            return;
                        }

                        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                        if (!$user->hasMana($cost)) {
                            $player->sendMessage("§cYou dont have enough mana to upgrade OreGen to $new_level! §eRequired - §6" . number_format($cost) . " mana");
                        } else {
                            $user->removeMana($cost);
                            $tile->level1 = $new_level;
                            $newsecs = $tile->getDelayByLevel($new_level) / 20;
                            $tile->setDelay((int) $newsecs);
                            $player->sendMessage("§aSuccessfully upgraded OreGen to $new_level. §aUsed §6" . number_format($cost) . " mana");
                        }
                    }
                };
                $this->sendModalForm($player, "Confirm Upgrade", $content, [], $bfunc);
            }
        };
        $form->setCallable($func);
        //$this->sendSimpleForm($player,"§b§lShop","upgrade oregen",$buttons,$func);
        $player->sendForm($form);
    }

    public function sendSpawnerUpgrade(Player $player, Block $block) : void {
        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
        if (!$tile instanceof MobSpawner) return;
        $level = $tile->level1;
        $details = "§eCurrent Level - §7$level\n§eCurrent Entity Delay - §7{$tile->getDelayInSeconds()}s\n§eCurrent Entities per Spawn - §7{$tile->getSpawnCount()}";
        if ($level >= MobSpawner::MAX_LEVEL) {
            $player->sendMessage("§cError: That spawner is already on max level " . MobSpawner::MAX_LEVEL . "\n§bDetails: $details");
            return;
        }
        $percost = 12500;

        $form = new CustomForm(null);
        $form->setTitle("Upgrade Spawner");
        $form->addLabel("$details\n§ePerks:\n- §aLess Delay between entity spawn\n- §aMore entity spawn count");
        $ranges = range($level + 1, MobSpawner::MAX_LEVEL);
        $form->addDropdown("Select level", array_map('strval', $ranges));

        $func = function(Player $player, ?array $data) use ($block, $ranges, $percost) : void {
            if ($data !== null and isset($ranges[$data[1]])) {
                $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                if (!$tile instanceof MobSpawner) return;
                $level = $tile->level1;
                if ($level >= MobSpawner::MAX_LEVEL) {
                    $player->sendMessage("§cThat spawner is already on max level " . MobSpawner::MAX_LEVEL);
                    return;
                }

                $new_level = $ranges[$data[1]];
                if (($cost = $this->getUpgradeCost($level, $new_level, $percost)) === null) return;

                $content = "§fAre you sure you want to upgrade this §aSpawner?
                \n§eSpawner Level upgrade §7{$level} §a-> §7{$new_level}
                \n§eCost calculated -> §a" . number_format($cost) . " mana";
                $bfunc = function(Player $player, ?bool $data) use ($block, $new_level, $cost) : void {
                    if ($data) {
                        $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                        if (!$tile instanceof MobSpawner) return;
                        $level = $tile->level1;
                        if ($level >= MobSpawner::MAX_LEVEL) {
                            $player->sendMessage("§cThat spawner is already on max level " . MobSpawner::MAX_LEVEL);
                            return;
                        }

                        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                        if (!$user->hasMana($cost)) {
                            $player->sendMessage("§cYou dont have enough mana to upgrade Spawner to $new_level! §eRequired - §6" . number_format($cost) . " mana");
                        } else {
                            $user->removeMana($cost);
                            $tile->level1 = $new_level;
                            $tile->setSpawnCount($new_level);
                            $newsecs = (int) $tile->getDelayByLevel($new_level) / 20;
                            $tile->setDelay($newsecs);
                            $player->sendMessage("§aSuccessfully upgraded Spawner to $new_level. §aUsed §6" . number_format($cost) . " mana");
                        }
                    }
                };
                $this->sendModalForm($player, "Confirm Upgrade", $content, [], $bfunc);
            }
        };
        $form->setCallable($func);
        $player->sendForm($form);
    }

    public function dataToMenu(Player $player, string $ogtitle, string $title, array $total, $datum) : void {
        if (is_array($datum)) {
            $categories = array_keys($datum);
            $func = function(Player $player, ?int $data) use ($datum, $ogtitle, $total, $categories) : void {
                if ($data !== null && isset($categories[$data])) {
                    $this->dataToMenu($player, $ogtitle, $categories[$data], $total, $datum[$categories[$data]]);
                }
            };
            $this->sendSimpleForm($player, $title, "", $categories, $func);
        } elseif (is_string($datum)) {
            $form = new CustomForm(null);
            $form->setTitle($title);
            $to_send = "";
            $skip = false;
            $i = 0;
            $exploded = explode("\n", $datum);
            if (count($exploded) === 1) {
                $to_send .= "➼ " . TextFormat::WHITE . $exploded[0];
            } else {
                foreach ($exploded as $fstr) {
                    if ($fstr === "") continue;
                    if ($skip) {
                        $to_send .= TextFormat::WHITE . $fstr . "\n";
                        $skip = false;
                        continue;
                    }
                    if (str_ends_with($fstr, ":")) $skip = true;
                    $to_send .= "➼ " . TextFormat::GREEN . ++$i . ". " . TextFormat::WHITE . $fstr . "\n";
                }
            }
            $form->addLabel($to_send);
            $form->setCallable(function(Player $player, ?array $data) use ($ogtitle, $total) : void {
                if ($data !== null) {
                    $this->dataToMenu($player, $ogtitle, $ogtitle, $total, $total);
                }
            }
            );
            $player->sendForm($form);
        }
    }

    public function sendServersMenu(Player $player) {
        $buttons = ["§l§bFallenTech §3Hub", "§l§bFallenTech §cFactions", "§l§bFallenTech §aPractice"];
        $servers = ["§cRed" => 19134];
        foreach ($servers as $server => $port) {
            if ($server == $this->pl->sbtype) continue;
            $buttons[] = "§l§bFallenTech §eSkyBlock $server";
        }
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                if ($this->pl->getChair()->isSitting($player)) {
                    $this->sendMessage($player, "§cCannot transfer while sitting!");
                    return;
                }
                $index = $data + 1;
                switch ($index) {
                    case 1:
                        //                        $this->pl->transferToLobby($player);
                        break;
                    case 2:
                        $this->pl->transfer($player, "factions");
                        break;
                    case 3:
                        $this->pl->transfer($player, "minigames");
                        break;
                    case 4:
                        $servers = ["§cRed" => "sb_red"];
                        foreach ($servers as $server => $sname) {
                            if ($server == $this->pl->sbtype) {
                                unset($servers[$server]);
                            }
                        }
                        $index -= 3;
                        $i = 1;
                        foreach ($servers as $sname) {
                            if ($index == $i) {
                                $this->pl->transfer($player, $sname);
                            }
                            ++$i;
                        }
                        break;
                    default:
                        break;
                }
            }
        };
        $this->sendSimpleForm($player, "§bOur Servers", "§6Select a Server to transfer to -", $buttons, $func);
    }

    public function sendUserIslandsMenu(Player $player) {
        $buttons = $islands = [];
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        foreach ($user->getIslands() as $island) {
            is_null($this->pl->getIslandManager()->getOnlineIsland($island)) ? $mod = "§4$island" : $mod = "§a$island";
            $buttons[] = $mod;
            $islands[] = $island;
        }
        $func = function(Player $player, ?int $data) use ($islands) : void {
            if ($data !== null) {
                if (isset($islands[$data])) {
                    $islandName = $islands[$data];
                    if (($island = $this->pl->getIslandManager()->getOnlineIsland($islandName)) !== null) {
                        if (!$island->isMember($player->getName())) {
                            $error = "You arent member of that island anymore to teleport!";
                            $this->sendResultForm($player, $error, "sendUserIslandsMenu");
                        } else {
                            $owner = $island->getOwner();
                            if (is_null($island->getWorldLevel())) {
                                $error = "World not loaded!";
                                $this->sendResultForm($player, $error, "sendUserIslandsMenu");
                                return;
                            }
                            if ($player->getWorld()->getDisplayName() == 'PvP') {
                                $error = "Youre in PvP world!";
                                $this->sendResultForm($player, $error, "sendUserIslandsMenu");
                                return;
                            }
                            $island->teleport($player);
                            if (($user = $this->pl->getUserManager()->getOnlineUser($owner)) !== null) {
                                $this->pl->sendMessage($user->getPlayer(), "§a{$player->getName()} §ejust teleported to your island by /is tp!");
                            }
                            $this->sendMessage($player, "§eYou teleported to island §a{$islandName}§e's spawn successfully");
                        }
                    } else {
                        $error = "Island is offline!";
                        $this->sendResultForm($player, $error, "sendUserIslandsMenu");
                    }
                }
            }
        };
        $this->sendSimpleForm($player, "§e§lIslands you are member of -", "§6Select an island to teleport to -", $buttons, $func);
    }

    public function sendMessage(Player $sender, string $message) : void {
        $sender->sendMessage(Values::FT_PREFIX . $message);
    }

    public function sendTagMainMenu(Player $player) {
        $buttons = ["§8Reset Tag"];
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        foreach ($this->pl->getTagManager()->getTags() as $id => $tag) {
            $tag = TextFormat::clean($tag);
            if ($user->getSelTag() == $id) $buttons[] = "§a" . $tag;
            elseif ($user->hasTag($id)) $buttons[] = "§8" . $tag;
            else    $buttons[] = "§4" . $tag;
        }
        $func = function(Player $player, ?int $data) use ($user) {
            if ($data !== null) {
                if ($data == 0) {
                    $user->setSelTag(-1);
                    $player->sendMessage("§aTag unset successfully!");
                    return;
                }
                if ($user->hasTag(--$data)) {
                    $user->setSelTag($data);
                    $player->sendMessage("§aEquipped tag §7[{$this->pl->getTagManager()->getTagString($data)}§7] §asuccessfully!");
                } else {
                    $player->sendMessage("§cYou dont have the §7[{$this->pl->getTagManager()->getTagString($data)}§7] §ctag to equip! §6Get tags from Crates or Dropparty/Envoys");
                }
            }
        };
        $this->sendSimpleForm($player, "§e§lTags", "§6Select a Tag to equip -", $buttons, $func);
    }

    public function sendKitTypeMenu(Player $player) {
        $func = function(Player $player, ?int $data) {
            if ($data !== null) {
                if ($data == 0) $this->sendKitMainMenu($player);
                else  $this->sendGKitMainMenu($player);
            }
        };
        $this->sendSimpleForm($player, "§e§lKits", "§6Select a Kit type -", ["§f§lStandard Kits", "§b§lGKits"], $func);
    }

    public function sendKitMainMenu(Player $player) {
        $buttons = $kits = [];
        foreach ($this->pl->kits as $name => $data) {
            $name = ucfirst($name);
            $lower = strtolower($name);
            ($player->hasPermission("core.kit.$lower")) ? $mod = "§f$name" : $mod = "§4$name";
            $buttons[] = $mod;
            $kits[] = $name;
        }
        $func = function(Player $player, ?int $data) use ($kits) {
            if ($data !== null) {
                if (isset($kits[$data])) {
                    $kit = $kits[$data];
                    $this->sendKitInfo($player, $kit);
                }
            }
        };
        $this->sendSimpleForm($player, "§e§lKits", "§6Select a Kit for info -", $buttons, $func);
    }

    public function sendKitInfo(Player $player, string $kit) {
        $info = "";
        $kits = $this->pl->getKit($kit);
        if ($kits !== null) {
            $left = $kits->getCoolDownLeft($player);
            if ($left !== null) {
                $info = "§cCooldown left = §7$left\n§f" . $kits->data["info"];
            } else {
                $info = $kits->data["info"];
            }
        }
        $func = function(Player $player, ?bool $data) use ($kit) {
            if ($data !== null) {
                if ($data) {
                    $kits = $this->pl->getKit($kit);
                    if ($kits !== null) {
                        $name = $kits->getName();
                    } else {
                        $this->sendResultForm($player, "Session Error! Try again!", "sendKitMainMenu");
                        return;
                    }
                    $lower = strtolower($name);
                    if (!$player->hasPermission("core.kit.$lower")) {
                        if ($lower != "partner") {
                            $rank = ucfirst($name);
                            $name = $kits->getName();
                            if ($name == 'voter') $error = "You haven't voted today! VoteKey on vote.fallentech.io to unlock this kit by /vote!";
                            else    $error = "You don't have permission to use this kit! Get $name kit at $rank Rank! Get premium ranks from shop.fallentech.io";
                            $this->sendResultForm($player, $error, "sendKitMainMenu");
                            return;
                        } else {
                            if (!$this->pl->staffapi->isPartner($player->getName())) {
                                $error = "You don't have permission to use this kit! Get Partner kit at a Partner Rank(Youtuber, Streamer...)! Apply at discord.fallentech.io";
                                $this->sendResultForm($player, $error, "sendKitMainMenu");
                                return;
                            }
                        }
                    }
                    $left = $kits->getCoolDownLeft($player);
                    if ($left !== null) {
                        $error = "Kit $name is in cooldown for you at the moment!\nYou will be able to get it again in $left";
                        $this->sendResultForm($player, $error, "sendKitMainMenu");
                        return;
                    }
                    if ($player->getWorld()->getDisplayName() == "PvP") {
                        $error = "You can't claim the kit here!";
                        $this->sendResultForm($player, $error, "sendKitMainMenu");
                        return;
                    }
                    $kits->addTo($player);
                } else {
                    $this->sendKitMainMenu($player);
                }
            }
        };
        $this->sendModalForm($player, "Do you want to select this kit?", $info, ["Yes", "No"], $func);
    }

    public function sendGKitMainMenu(Player $player) {
        $buttons = $gkit = [];
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        foreach ($this->pl->gkits as $name => $data) {
            $name = ucfirst($name);
            $lower = strtolower($name);
            $string = "§b$name\n§6- §a" . $user->getKitCount($name) . " §fleft §6-";
            $buttons[] = $string;
            $gkit[] = $lower;
        }
        $func = function(Player $player, ?int $data) use ($gkit) {
            if ($data !== null) {
                if (isset($gkit[$data])) {
                    $name = $gkit[$data];
                    $upper = ucfirst($name);
                    $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
                    if ($player->getWorld()->getDisplayName() == "PvP") {
                        $this->sendMessage($player, "§4[Error]§c You can't claim the kit here!");
                        return;
                    }
                    if ($this->pl->getFunctions()->isInventoryFull($player)) {
                        $this->sendMessage($player, "§4[Error]§c Your inventory is full! Empty a slot first to claim!");
                        return;
                    }
                    if ($user->hasKit($name)) {
                        $user->removeKitCount($name);
                        $this->pl->getFunctions()->giveKitChest($player, $name);
                        $result = "§fYou successfully claimed §a$upper §fGKit! §aCheck your inventory for the gkit chest, place the chest and open it to get the items!\n§eGet GKits access from Relics or MysticKey crate or buying from §bshop.fallentech.io!";
                    } else {
                        $result = "§cYou have §d0 §a$upper §cGKit access! §aGet GKits access from /goals or buying from §bshop.fallentech.io!\n§eGKits give Custom enchanted sword and full armor with some extra stuff!";
                    }
                    $this->sendResultForm($player, $result, "sendGKitMainMenu");
                }
            }
        };
        $this->sendSimpleForm($player, "§e§lGKits", "§6Select a GKit for info -", $buttons, $func);
    }

    public function sendUpgradeMenu(Player $player) : void {
        $buttons = ["Spawner Upgrade", "AutoMiner Upgrade", "OreGen Upgrade", "AutoSeller Upgrade"];
        $func = function(Player $player, ?int $data) {
            if ($data !== null) {
                switch ($data) {
                    case 0:
                        $this->pl->upd_touch[$player->getName()] = "spawner";
                        break;
                    case 1:
                        $this->pl->upd_touch[$player->getName()] = "autominer";
                        break;
                    case 2:
                        $this->pl->upd_touch[$player->getName()] = "oregen";
                        break;
                    case 3:
                        $this->pl->upd_touch[$player->getName()] = "autoseller";
                        break;
                    default:
                        return;
                }
                $player->sendMessage("§b> §ePlease tap the " . ucfirst($this->pl->upd_touch[$player->getName()]) . " you would like to upgrade.");
            }
        };
        $this->sendSimpleForm($player, "§6Upgrade Custom Blocks", "§3Select a type -", $buttons, $func);
    }

    public function sendUpdateWindow(Player $player) {
        $form = new CustomForm(null);
        $form->setTitle("§e§lUpdates:");
        $i = 0;
        foreach ($this->pl->updates['updates'] as $data) {
            ++$i;
            $form->addLabel("§f$i. §b" . $data);
        }
        $form->addLabel("§aStay Connected:\n\n§f- §dDiscord: §ehttps://discord.fallentech.io\n\n§f- §bTwitter: §f@§aRealFallenTech");
        $player->sendForm($form);
    }

    /**
     * @param Player $player
     */
    public function sendWarpsMain(Player $player) : void {
        $warps = $buttons = [];
        $warp = $this->pl->warps;
        $canedit = isset($this->pl->gandalf->edit[$player->getName()]);
        foreach ($warp as $name => $data) {
            if (isset($data['op'])) {
                if ($this->pl->hasOp($player) or $canedit) {
                    $buttons[] = ucfirst($name);
                    $warps[] = $name;
                }
            } else {
                $buttons[] = ucfirst($name);
                $warps[] = $name;
            }
        }
        $func = function(Player $player, ?int $data = null) use ($warps) : void {
            if ($data !== null) {
                if (isset($warps[$data])) {
                    $warp = $warps[$data];
                    $this->sendWarpsConfirm($player, $warp);
                }
            }
        };
        $this->sendSimpleForm($player, "§e§lWarp to a place -", "§6Choose a warp -", $buttons, $func);
    }

    public function sendWarpsConfirm(Player $player, string $warp) {
        if ($warp === "info") {
            $this->dataToMenu($player, "Tutorial", "Tutorial", $this->pl->tutorial, $this->pl->tutorial);
            return;
        }
        $message = '§eAre you sure you want to teleport to §a' . ucfirst($warp) . '§e?';
        if ($warp == 'dropparty' or $warp == 'warzone' or $warp == 'nether') $message .= "\n§cPvP is enabled there!";
        $func = function(Player $player, ?bool $data) use ($warp) : void {
            if ($data) {
                $warps = $this->pl->warps;
                if (!isset($warps[strtolower($warp)])) {
                    $this->sendMessage($player, "§cWarp not found!");
                    return;
                }
                $canedit = isset($this->pl->gandalf->edit[$player->getName()]);
                if (isset($warps[$warp]['op'])) {
                    if (!$this->pl->hasOp($player) and !$canedit) {
                        $this->sendMessage($player, "§cNo permission");
                        return;
                    }
                }
                if ($player->getWorld()->getDisplayName() == "PvP" or $this->pl->isInCombat($player)) {
                    $this->sendMessage($player, "§cCant warp!");
                    return;
                }
                if ($warp != 'dropparty' and $warp != 'warzone' and $warp != "nether") {
                    $pos = new Location((double) $warps[$warp]['x'], (double) $warps[$warp]['y'], (double) $warps[$warp]['z'], $this->pl->getServer()->getWorldManager()->getWorldByName($warps[$warp]['world']), 0.0, 0.0);
                    $this->sendMessage($player, "§eWarping to §a" . ucfirst($warp));
                    $pos->getWorld()->loadChunk($pos->getFloorX() >> 4, $pos->getFloorZ() >> 4);
                    $player->teleport($pos, 0.0, 0.0);
                    $this->sendMessage($player, "§eWarp Successful!");
                } else if ($warp == 'dropparty') {
                    $randpos = mt_rand(0, 6);
                    $time = $this->pl->droppartyTimer;
                    if ($time < 0) $this->sendMessage($player, "§eWarping to DropParty Arena!\n§eDropParty already happened for this restart, next Dropparty after restart!");
                    else    $this->sendMessage($player, "§eWarping to DropParty Arena!\n§eDropParty will start in $time mins");
                    $pos = new Location((double) $warps[$warp][$randpos]['x'], (double) $warps[$warp][$randpos]['y'] + 1, (double) $warps[$warp][$randpos]['z'], $this->pl->getServer()->getWorldManager()->getWorldByName("PvP"), 0.0, 0.0);
                    $pos->getWorld()->loadChunk($pos->getFloorX() >> 4, $pos->getFloorZ() >> 4);
                    $player->teleport($pos, 0.0, 0.0);
                    $this->sendMessage($player, "§eWarp Successful!");
                } else if ($warp == 'warzone') {
                    $randpos = mt_rand(0, 6);
                    $this->sendMessage($player, "§aWarping to WarZone...");
                    $pos = new Location((double) $warps[$warp][$randpos]['x'], (double) $warps[$warp][$randpos]['y'] + 1, (double) $warps[$warp][$randpos]['z'], $this->pl->getServer()->getWorldManager()->getWorldByName("PvP"), 0.0, 0.0);
                    $pos->getWorld()->loadChunk($pos->getFloorX() >> 4, $pos->getFloorZ() >> 4);
                    $player->teleport($pos, 0.0, 0.0);
                    if ($this->pl->envoyTimer == -1) $status = "next restart!";
                    else    $status = "§b{$this->pl->envoyTimer} §emins!";
                    $this->sendMessage($player, "§aWarp Successful! §eEnvoys will spawn in $status!");
                } else if ($warp == 'nether') {
                    if (isset($this->pl->netherwarp[$player->getName()])) {
                        $this->sendMessage($player, "§cError: Please wait while previous nether warp request is executed!");
                        return;
                    }
                    $level = $this->pl->getServer()->getWorldManager()->getWorldByName(Values::NETHER_WORLD);
                    $spawn = $level->getSpawnLocation();
                    $x = $spawn->getX();
                    $z = $spawn->getZ();
                    $offset = Values::NETHER_SPAWN_RADIUS;
                    $randX = mt_rand($x - $offset, $x + $offset);
                    $randZ = mt_rand($z - $offset, $z + $offset);
                    $pos = new Position($randX, 60, $randZ, $level);
                    $pos->getWorld()->loadChunk($pos->getFloorX() >> 4, $pos->getFloorZ() >> 4);
                    $this->pl->netherwarp[$player->getName()] = true;
                    if ($level->isChunkGenerated($pos->getFloorX() >> 4, $pos->getFloorZ() >> 4)) {
                        $this->teleportToNether($player, $level, $pos);
                        return;
                    }
                    $level->orderChunkPopulation($pos->getFloorX() >> 4, $pos->getFloorZ() >> 4, null)->onCompletion(function() use ($player, $pos, $level) : void {
                        $this->teleportToNether($player, $level, $pos);
                    }, function() use ($player) : void {
                        unset($this->pl->netherwarp[$player->getName()]);
                        if ($player->isOnline())
                            $this->sendMessage($player, "§cError: Couldn't teleport you to nether!");
                    }
                    );
                }
            }
        };
        $this->sendModalForm($player, "§e§lWarp confirm -", $message, ["Yes", "No"], $func);
    }

    /**
     * @param Player   $player
     * @param World    $level
     * @param Position $pos
     */
    public function teleportToNether(Player $player, World $level, Position $pos) : void {
        $str = "§cNether world has been disabled this reset!";
        $player->sendMessage($str);
        //        $spawn = $level->getSafeSpawn($pos);
        //        unset($this->pl->netherwarp[$player->getName()]);
        //        if (!$player->isOnline()) return;
        //
        //        $player->teleport($spawn, 0.0, 0.0);
        //        if ($this->pl->netherReset > time()) {
        //            $reset = "in " . Util::getTimePlayed($this->pl->netherReset - time());
        //        } else {
        //            $reset = "next restart!";
        //        }
        //        $this->pl->nether_invinc[$player->getName()] = time() + Values::NETHER_INVINCIBILITY;
        //        $str = "§cNether world will auto reset §7" . $reset;
        //        $str .= "\n§7You can set homes here using /sethome";
        //        $player->sendMessage($str);
        //        $player->sendTitle("§c§l§oNether§r", "§fYou have §b" . Values::NETHER_INVINCIBILITY . " secs §fof invincibility!" . "\n" . "§eBorder - §7" . Values::NETHER_BORDER . " /pos", 20, 20, 20);

    }
}