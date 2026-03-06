<?php


namespace SkyBlock\command;


use pmmp\RconServer\RconCommandSender;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Koth extends BaseCommand {
    /**
     * Koth constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'koth');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (isset($args[0])) {
            switch ($args[0]) {
                case "setspawn":
                    if (!$sender instanceof Player) {
                        $this->sendMessage($sender, self::NO_CONSOLE);
                        return;
                    }
                    if (!Main::getInstance()->hasOp($sender)) {
                        $this->sendMessage($sender, self::NO_PERMISSION);
                        return;
                    }
                    Main::getInstance()->setPoint($sender, "spawn");
                    $this->sendMessage($sender, TextFormat::YELLOW . " Spawn point added!");
                    break;
                case "c1":
                    if (!$sender instanceof Player) {
                        $this->sendMessage($sender, self::NO_CONSOLE);
                        return;
                    }
                    if (!Main::getInstance()->hasOp($sender)) {
                        $this->sendMessage($sender, self::NO_PERMISSION);
                        return;
                    }
                    Main::getInstance()->setPoint($sender, "c1");
                    $this->sendMessage($sender, TextFormat::YELLOW . "Corner 1 added!");
                    break;
                case "c2":
                    if (!$sender instanceof Player) {
                        $this->sendMessage($sender, self::NO_CONSOLE);
                        return;
                    }
                    if (!Main::getInstance()->hasOp($sender)) {
                        $this->sendMessage($sender, self::NO_PERMISSION);
                        return;
                    }
                    Main::getInstance()->setPoint($sender, "c2");
                    $this->sendMessage($sender, TextFormat::YELLOW . "Corner 2 added!");
                    break;
                case "save":
                    if (!$sender instanceof Player) {
                        $this->sendMessage($sender, self::NO_CONSOLE);
                        return;
                    }
                    if (!Main::getInstance()->hasOp($sender)) {
                        $this->sendMessage($sender, self::NO_PERMISSION);
                        return;
                    }
                    if (!Main::getInstance()->isRunning()) {
                        if (Main::getInstance()->saveKOTH()) {
                            $this->sendMessage($sender, TextFormat::YELLOW . "KOTH config saved!");
                        } else {
                            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Error while saving KOTH config!");
                        }
                    } else {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " KOTH config cannot be saved while KOTH is running!");
                    }
                    break;
                case "start":
                    if ($sender instanceof RconCommandSender) {
                        if (!isset($args[1])) {
                            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /koth start <staff>");
                            break;
                        }
                        $staffName = $args[1];
                    } else {
                        $staffName = $sender->getName();
                        if (!Main::getInstance()->staffapi->isStaff($staffName)) {
                            $this->sendMessage($sender, self::NO_PERMISSION);
                            return;
                        }
                    }
                    $timeLeft = (int) (Main::getInstance()->gandalf->rtime / 60);
                    if ($timeLeft <= 12) {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " There are only $timeLeft minutes left till restart. Start KOTH next restart!");
                        return;
                    }
                    if (Main::getInstance()->isRunning()) {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " KOTH is already running!");
                        return;
                    } elseif (Main::getInstance()->startArena()) {
                        Main::getInstance()->sendDiscordMessage(
                            "KOTH Started!",
                            "King of the Hill #`" . Main::getInstance()->kothnumber . "` was started\nStarted by **$staffName**\n\nJoin now to fight for the top spot!\n",
                            1,
                            "<@&1277628725424427141> :loudspeaker:"
                        );
                        $this->sendMessage($sender, TextFormat::YELLOW . "KOTH started!");
                    } else {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " KOTH has not been fully setup yet!");
                    }
                    break;
                case "stop":
                    if ($sender instanceof RconCommandSender) {
                        if (!isset($args[1])) {
                            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /koth stop <staff>");
                            return;
                        }
                        $staffName = $args[1];
                    } else {
                        $staffName = $sender->getName();
                        if (!Main::getInstance()->staffapi->isHardStaff($staffName)) {
                            $this->sendMessage($sender, self::NO_PERMISSION);
                            break;
                        }
                    }
                    if (Main::getInstance()->forceStop()) {
                        $this->sendMessage($sender, TextFormat::YELLOW . " KOTH has been forcefully stopped!");
                    } else {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " KOTH has not been fully setup yet!");
                    }
                    break;
                case "number":
                    $this->sendMessage($sender, TextFormat::YELLOW . "KOTH is on #`" . Main::getInstance()->kothnumber . "`");
                    break;
            }
        }
        if ($sender instanceof Player) {
            if (!isset($args[0])) {
                $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /koth <prizes / help / join / leave / time>");
                return;
            }
            if ($args[0] === "join") {
                if (Main::getInstance()->isInCombat($sender)) {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use this command in combat!");
                    return;
                }
                if (Main::getInstance()->sendToKoth($sender)) {
                    $this->sendMessage($sender, TextFormat::YELLOW . "You have joined KOTH. Be the first to capture the hill!\nCapture the hill by standing on the top for 90 seconds. Good luck!");
                } else {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " KOTH is not running currently. Use /koth time to see the time left till the next KOTH!");
                }
            }
            if ($args[0] === "leave") {
                if (Main::getInstance()->isInArena($sender)) {
                    if (Main::getInstance()->removePlayer($sender)) {
                        Main::getInstance()->teleportToSpawn($sender);
                        $this->sendMessage($sender, TextFormat::YELLOW . "You have left KOTH!");
                    } else {
                        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot leave KOTH!");
                    }
                } else {
                    $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You are not in KOTH!");
                }
            }
            if ($args[0] === "help") {
                $this->sendMessage($sender, TextFormat::YELLOW . "KOTH - King of the Hill! Be the first to capture the hill!\nCapture the hill by standing on the top for 90 seconds. Good luck!");
            }
            if ($args[0] === "prize" || $args[0] === "prizes") {
                $this->sendMessage($sender, TextFormat::YELLOW . "The KOTH prizes are; money, enchant books, a trophy, a crown, scrolls, crate keys, and more!");
            }
            if ($args[0] === "time") {
                $this->sendMessage($sender, TextFormat::YELLOW . "KOTH is started by staff every few hours. Ask them or stay alert on the Discord!");
            }
        }
    }
}