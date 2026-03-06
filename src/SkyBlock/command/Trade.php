<?php


namespace SkyBlock\command;


use pocketmine\block\BlockTypeIds;
use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\Main;
use SkyBlock\user\User;

class Trade extends BaseCommand {

    /**
     * Trade constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "trade", "Trade cmd", 'help');
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
        switch (strtolower($args[0])) {
            case "up":
            case "put":
            case "add":
                if (!isset($args[1]) or isset($args[3])) {
                    $this->sendMessage($sender, "§6Usage: /trade add <type = money, mana, mobcoin, helditem> [value]");
                    return;
                }
                $type = strtolower($args[1]);
                $types = ["money", "mana", "mobcoin", "helditem"];
                if (!in_array($args[1], $types, true)) {
                    $this->sendMessage($sender, "§4[Error]§c Accepted types = " . implode(", ", $types) . "\n§6Usage: /trade add <type = money, mana, mobcoin, helditem> [value]");
                    return;
                }
                if (!isset($args[2])) { // $type !== "helditem" &&  tempo fix
                    $this->sendMessage($sender, "§4[Error]§c No value specified for $type\n§6Usage: /trade add <type = money, mana, mobcoin, helditem> [value]");
                    return;
                }
                if (isset($args[2]) && (!is_int((int) [2]) or $args[2] <= 0)) {
                    $this->sendMessage($sender, "§4[Error]§c Enter a value greater than 0!");
                    return;
                }
                $value = (int) $args[2] ?? null;
                if ($this->hasATrade(strtolower($sender->getName()))) {
                    $this->sendMessage($sender, "§4[Error]§c You already have a trade! Take it off by /trade takeoff");
                    return;
                }
                if ($type === 'helditem') {
                    $item = $sender->getInventory()->getItemInHand();
                    if (in_array($item->getTypeId(), [BlockTypeIds::CHEST, BlockTypeIds::AIR]) || $item->isNull()) {
                        $this->sendMessage($sender, "§4[Error]§c Item in hand not valid to put up for trade.");
                        return;
                    }
                    if ($item instanceof Armor or $item instanceof Tool) $item = $this->func->renameItem($item, "");
                    $name = $this->func->getCleanName($item);
                    $tradedata['item'] = $item->nbtSerialize();
                    $tradedata["damage"] = $tradedata["damage"] ?? 0;
                    $tradedata["count"] = $item->getCount() ?? 1;
                    $tradedata['trader'] = strtolower($sender->getName());
                    $tradedata['type'] = 'item';
                    $tradedata['name'] = $name;
                    $sender->getInventory()->setItemInHand(VanillaItems::AIR());
                    $this->sendMessage($sender, '§eYou have successfully put your §e' . $name . ' §r§7(§cx§e' . $item->getCount() . '§7) §eup for trade.');
                } else {
                    $user = $this->um->getOnlineUser($sender->getName());
                    switch ($type) {
                        case "money":
                            if (!$user->removeMoney($value)) {
                                $this->sendMessage($sender, "§4[Error]§c You dont have §6" . number_format($value) . "$ §cto put on trade.");
                                return;
                            }
                            $this->sendMessage($sender, '§eYou have successfully put §6' . number_format($value) . '$ §eup for trade.');
                            break;
                        case "mana":
                            if (!$user->removeMana($value)) {
                                $this->sendMessage($sender, "§4[Error]§c You dont have §6" . number_format($value) . " mana §cto put on trade.");
                                return;
                            }
                            $this->sendMessage($sender, '§eYou have successfully put §6' . number_format($value) . ' §amana §eup for trade.');
                            break;
                        case "mobcoin":
                            if (!$user->removeMobCoin($value)) {
                                $this->sendMessage($sender, "§4[Error]§c You dont have §6" . number_format($value) . " coins §cto put on trade.");
                                return;
                            }
                            $this->sendMessage($sender, '§eYou have successfully put §6' . number_format($value) . ' §acoins §eup for trade.');
                            break;
                    }
                    $tradedata = [
                        'trader' => strtolower($sender->getName()),
                        'type'   => $type,
                        'value'  => $value
                    ];
                }
                $this->pl->trades[] = $tradedata;
                break;
            case "offers":
                if (isset($args[1])) {
                    $this->sendMessage($sender, "§6Usage: /trade offers");
                    break;
                }
                if (!$this->hasATrade(strtolower($sender->getName()))) {
                    $this->sendMessage($sender, "§4[Error]§c You dont have a trade!");
                    return;
                }
                $tid = $this->getTradeId(strtolower($sender->getName()));
                if (!isset($this->pl->trade_offers[$tid])) {
                    $this->sendMessage($sender, "§4[Error]§c No Trade offers received!");
                    return;
                }
                $this->formfunc->sendTradeOffers($sender, $tid);
                break;
            case "takeoff":
                if (isset($args[1])) {
                    $this->sendMessage($sender, "§6Usage: /trade takeoff");
                    break;
                }
                if (!$this->hasATrade(strtolower($sender->getName()))) {
                    $this->sendMessage($sender, "§4[Error]§c You dont have a trade!");
                    return;
                }
                $this->takeOff($sender, $this->getTradeId(strtolower($sender->getName())));
                break;
            case "list":
                if (isset($args[1])) {
                    $this->sendMessage($sender, "§6Usage: /trade list");
                    break;
                }
                $this->formfunc->sendTradeList($sender);
                break;
            case "info":
                if (!isset($args[1])) {
                    $this->sendMessage($sender, "§6Usage: /trade info <player name>");
                    break;
                }
                $playerName = strtolower($args[1]);
                if (!$this->hasATrade($playerName)) {
                    $this->sendMessage($sender, "§4[Error]§c That player doesnt have a trade!");
                    return;
                }
                if ($playerName === strtolower($sender->getName())) {
                    $this->sendMessage($sender, "§4[Error]§c Use /trade view to see your trade!");
                    return;
                }
                $this->formfunc->sendTradeInfo($sender, $this->getTradeId($playerName));
                break;
            case "view":
                if (isset($args[1])) {
                    $this->sendMessage($sender, "§6Usage: /trade view");
                    break;
                }
                if (!$this->hasATrade(strtolower($sender->getName()))) {
                    $this->sendMessage($sender, "§4[Error]§c You dont have a trade to view!");
                    return;
                }
                $this->formfunc->sendTradeInfo($sender, $this->getTradeId(strtolower($sender->getName())), false);
                break;
            default:
                $this->sendMessage($sender, "§6Usage: /trade <add / takeoff / list / info / view>");
                break;
        }
    }

    /**
     * @param Player $sender
     * @param string $tid
     */
    private function takeOff(Player $sender, string $tid) : void {
        $trade = $this->pl->trades[$tid];
        if ($trade['type'] === 'item') {
            if ($this->func->isInventoryFull($sender)) {
                $this->sendMessage($sender, "§4[Error]§c Your inventory is full! Empty a slot first.");
                return;
            }
            $sender->getInventory()->addItem(Item::nbtDeserialize($trade['item']));
        } else {
            $user = $this->um->getOnlineUser($sender->getName());
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
            }
        }
        $this->sendMessage($sender, '§eYou have successfully taken off your trade!');
        unset($this->pl->trades[$tid]);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function hasATrade(string $name) : bool {
        foreach ($this->pl->trades as $trade) {
            if ($trade['trader'] === $name) return true;
        }
        return false;
    }

    /**
     * @param string $name
     *
     * @return int
     */
    private function getTradeId(string $name) : int {
        foreach ($this->pl->trades as $tid => $trade) {
            if ($trade['trader'] === $name) return $tid;
        }
        return -1;
    }

    /**
     * @param Item $item
     *
     * @return string
     */
    public static function itemInfoString(Item $item) : string {
        $extra = implode(",", $item->getLore()) ?? "";
        $used = $item->getStateId() > 0 ? 'Yes' : 'No';
        $enchanted = count($item->getEnchantments()) > 0 ? 'Yes' : 'No';
        $citem = clone($item);
        if ($citem instanceof Tool or $citem instanceof Armor)
            $toSend = TF::AQUA . 'Item: ' . TF::YELLOW . $citem->getName() . TF::DARK_GRAY . "\n" . TF::RESET . TF::AQUA . 'Count: ' . TF::GREEN . $item->getCount() . "\n" .
                TF::AQUA . 'Used Item: ' . TF::GREEN . $used . "\n" .
                TF::AQUA . 'Enchanted: ' . TF::GREEN . $enchanted . "\n" .
                TF::AQUA . 'Extra Data: §7' . $extra . "\n";
        else
            $toSend = TF::AQUA . 'Item: ' . TF::YELLOW . $citem->getName() . TF::DARK_GRAY . "\n" . TF::RESET . TF::GREEN . $item->getCustomName() . "\n" . TF::RESET . TF::AQUA . 'Count: ' . TF::GREEN . $item->getCount() . "\n" .
                TF::AQUA . 'Used Item: ' . TF::GREEN . $used . "\n" .
                TF::AQUA . 'Enchanted: ' . TF::GREEN . $enchanted . "\n" .
                TF::AQUA . 'Extra Data: §7' . $extra . "\n";
        if ($enchanted == 'Yes') {
            $toSend .= TF::AQUA . 'Enchantments:' . "\n";
            $itemens = $item->getEnchantments();
            foreach ($itemens as $enchant) {
                $en = BaseEnchantment::getEnchantmentId($enchant);
                if ($en < 25) {
                    $name = Main::getInstance()->getFunctions()->numberToEnchantment($en);
                    $lev = $enchant->getLevel();
                    $toSend .= TF::YELLOW . "- " . $name . " " . TF::WHITE . $lev . "\n";
                }
                if ($en > 99 && $en < 175) {
                    $name = BaseEnchantment::getEnchantment($en)->getName();
                    $lev = $enchant->getLevel();
                    $toSend .= TF::GREEN . "- " . $name . " " . TF::WHITE . $lev . "\n";
                }
                if ($en >= 175) {
                    $name = BaseEnchantment::getEnchantment($en)->getName();
                    $lev = $enchant->getLevel();
                    $toSend .= TF::AQUA . "- " . $name . " " . TF::WHITE . $lev . "\n";
                }
            }
        }
        return $toSend;
    }

    /**
     * @param User   $user
     * @param string $otype
     * @param int    $ovalue
     *
     * @return bool
     */
    public static function checkTradeRequirements(User $user, string $otype, int $ovalue = 1) : bool {
        switch ($otype) {
            case "Money":
                if (!$user->hasMoney($ovalue)) return false;
                break;
            case "Mana":
                if (!$user->hasMana($ovalue)) return false;
                break;
            case "MobCoin":
                if (!$user->hasMobCoin($ovalue)) return false;
                break;
            case "Held Item":
                $item = $user->getPlayer()->getInventory()->getItemInHand();
                if (in_array($item->getTypeId(), [BlockTypeIds::CHEST, BlockTypeIds::AIR])) return false;
                break;
        }
        return true;
    }

}