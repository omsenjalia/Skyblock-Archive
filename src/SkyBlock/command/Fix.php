<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Fix extends BaseCommand {

    /** @var int */
    public const REQUEST_TIME_OUT = 30; //secs
    public static array $fixConfirm = [];

    /**
     * Fix constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'fix', 'Fix held item', '[player]', ['repair']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (isset($args[0])) {
            $playerName = strtolower($args[0]);
            if ($playerName === "confirm") {
                if (!isset(self::$fixConfirm[$sender->getName()])) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You were not sent a fix request!");
                    return;
                }
                $req = self::$fixConfirm[$sender->getName()]["requester"];
                $time = self::$fixConfirm[$sender->getName()]["time"];
                if (time() > $time) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That fix request has expired!");
                    unset(self::$fixConfirm[$sender->getName()]);
                    return;
                }
                $reqPlayer = Server::getInstance()->getPlayerExact($req);
                if ($reqPlayer instanceof Player) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " The requester is no longer online!");
                    unset(self::$fixConfirm[$sender->getName()]);
                    return;
                }
                $this->sendMessage($reqPlayer, TextFormat::YELLOW . $sender->getName() . " has accepted your fix request!");
                Main::getInstance()->getFunctions()->fixPlayerHand($reqPlayer, $sender);
                return;
            }
            if (!Main::getStaffAPI()->hasStaffRank($sender->getName())) {
                if (!$sender->hasPermission("core.fix.player") && !$sender->hasPermission("fix.player")) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy SkyLord rank on " . TextFormat::AQUA . "shop.fallentech.io");
                    return;
                }
            }
            $player = Server::getInstance()->getPlayerByPrefix($playerName);
            if ($player === null) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player is not online!");
                return;
            }
            if ($playerName === $player->getName()) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot send a fix request to yourself!");
                return;
            }
            if (isset(self::$fixConfirm[$player->getName()])) {
                $time = self::$fixConfirm[$player->getName()]["time"];
                if (time() < $time) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That player already has an outgoing fix request, ask them to use /fix confirm!");
                    return;
                }
            }
            self::$fixConfirm[$player->getName()]["time"] = time() + self::REQUEST_TIME_OUT;
            self::$fixConfirm[$player->getName()]["requester"] = $sender->getName();
            $this->sendMessage($sender, TextFormat::YELLOW . "Sent a fix request to " . $player->getName() . "!");
            $this->sendMessage($player, TextFormat::YELLOW . $sender->getName() . " has sent your a fix request. Do /fix confirm to fix your item!");
        } else {
            if (!Main::getStaffAPI()->hasStaffRank($sender->getName())) {
                if (!$sender->hasPermission("core.fix")) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You can't use this command! Buy SkyLord rank on " . TextFormat::AQUA . "shop.fallentech.io");
                    return;
                }
            }
            Main::getInstance()->getFunctions()->fixPlayerHand($sender, $sender);
        }
    }
}