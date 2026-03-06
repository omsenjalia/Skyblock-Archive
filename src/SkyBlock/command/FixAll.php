<?php


namespace SkyBlock\command;


use alvin0319\CustomItemLoader\CustomItems;
use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\item\Tool;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Data;
use SkyBlock\Main;
use SkyBlock\util\Lore;
use SkyBlock\util\Values;

class FixAll extends BaseCommand {

    /** @var int */
    public const MAX_TIMER = 3; // seconds3
    public const REQUEST_TIME_OUT = 30; //secs


    public static array $fixallconfirm = [];
    public static array $fixalltimer = [];

    /**
     * FixAll constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'fixall', 'Fix whole inventory', '<player>', true, ['fa']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (isset(self::$fixalltimer[$sender->getName()]) && time() < self::$fixalltimer[$sender->getName()]) {
            $left = self::$fixalltimer[$sender->getName()] - time();
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Please wait $left more seconds to use this command!");
            return;
        }
        if (!isset($args[0])) {
            $playerName = strtolower($args[0]);
            if ($playerName === "confirm") {
                if (!isset(self::$fixallconfirm[$sender->getName()])) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You were not sent a fix all request!");
                    return;
                }
                $req = self::$fixallconfirm[$sender->getName()]["requester"];
                $time = self::$fixallconfirm[$sender->getName()]["time"];
                if (time() > $time) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " The fix all request has timed out!");
                    unset(self::$fixallconfirm[$sender->getName()]);
                    return;
                }
                $reqPlayer = Server::getInstance()->getPlayerExact($req);
                if (!$reqPlayer instanceof Player) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " The requester is no longer online!");
                    unset(self::$fixallconfirm[$sender->getName()]);
                    return;
                }
                $this->sendMessage($reqPlayer, TextFormat::YELLOW . $sender->getName() . " accepted your fix all request!");
                $this->fixInventory($reqPlayer, $sender);
                return;
            }
            if (!Main::getStaffAPI()->hasStaffRank($sender->getName())) {
                if (!$sender->hasPermission("core.fix.all")) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy SkyLord rank on " . TextFormat::AQUA . "shop.fallentech.io");
                    return;
                }
            }
            $player = Server::getInstance()->getPlayerByPrefix($playerName);
            if ($player === null) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
                return;
            }
            if ($player->getName() === $sender->getName()) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot send a fix all request to yourself!");
                return;
            }
            if (isset(self::$fixallconfirm[$player->getName()])) {
                $time = self::$fixallconfirm[$player->getName()]["time"];
                if (time() < $time) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player already has a fix all request!");
                    return;
                }
            }
            self::$fixallconfirm[$player->getName()]["time"] = time() + self::REQUEST_TIME_OUT;
            self::$fixallconfirm[$player->getName()]["requester"] = $sender->getName();
            $this->sendMessage($sender, TextFormat::YELLOW . "A fix all request has been sent to " . $player->getName());
            $this->sendMessage($player, TextFormat::YELLOW . $sender->getName() . " has sent you a fix all request. Use /fixall confirm!");
        } else {
            if (!Main::getStaffAPI()->hasStaffRank($sender->getName())) {
                if (!$sender->hasPermission("core.fix.all")) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy SkyLord rank on " . TextFormat::AQUA . "shop.fallentech.io");
                    return;
                }
            }
            $this->fixInventory($sender, $sender);
        }
    }

    private function fixInventory(Player $sender, Player $player) : void {
        if ($player->getWorld()->getDisplayName() === Values::PVP_WORLD) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use this command here!");
            unset(self::$fixallconfirm[$player->getName()]);
            return;
        }
        $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
        $inventory = $player->getInventory();
        $cost = Data::$commandFixCost;
        $str = TextFormat::YELLOW . "Searching for broken items...\n";
        $str2 = TextFormat::YELLOW . "Searching for broken items...\n";
        foreach ($inventory->getContents() as $slot => $item) {
            if ($user->hasMoney($cost)) {
                if ($item instanceof Durable && ($item->getTypeId() === CustomItems::ELYTRA()->getTypeId() || $item instanceof Armor || $item instanceof Tool)) {
                    if ($item->getDamage() !== 0) {
                        $new = 1;
                        $max = Data::$commandFixMaxFix;
                        $fixLore = Lore::getLoreInfo($item->getLore(), Lore::FIX_LORE, Lore::FIX_STR);
                        if ($fixLore !== null) {
                            $data = explode("/", $fixLore);
                            if (count($data) < 2) {
                                continue;
                            }
                            [$cur, $max] = $data;
                            if ($cur >= $max) {
                                continue;
                            }
                            $cur = (int) $cur;
                            $new = $cur + 1;
                        }
                        Lore::setLoreInfo($item, Lore::FIX_LORE, Lore::FIX_STR . "$new/$max");
                        $user->removeMobCoin($cost);
                        $item->setDamage(0);
                        $inventory->setItem($slot, $item);
                        $str .= TextFormat::YELLOW . "Item was fixed for $$cost by " . $sender->getName() . "\n";
                        $str2 .= TextFormat::YELLOW . $player->getName() . "'s item was fixed for $$cost\n";
                    }
                }
            } else {
                $str .= TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You do not have $cost to fix an item!\n";
                $str2 .= TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . $sender->getName() . " does not have enough money\n";
                break;
            }
        }
        unset(self::$fixallconfirm[$player->getName()]);
        self::$fixalltimer[$sender->getName()] = time() + self::MAX_TIMER;
        if ($player->getXuid() !== $sender->getXuid()) {
            $this->sendMessage($player, $str);
            $this->sendMessage($sender, $str2);
        } else {
            $this->sendMessage($sender, $str);
        }
    }
}