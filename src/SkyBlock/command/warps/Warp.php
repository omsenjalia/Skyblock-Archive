<?php


namespace SkyBlock\command\warps;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class Warp extends BaseCommand {

    /**
     * Warp constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'warp', 'Warps List', '<warp>', true, ['warps']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if ($sender->getPosition()->getWorld()->getDisplayName() === "PvP") {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use this command in the PvP world!");
            return;
        }
        if (Main::getInstance()->isInCombat($sender)) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use this command in combat!");
            return;
        }
        if (!isset($args[0])) {
            Main::getInstance()->getFormFunctions()->sendWarpsMain($sender);
        } else {
            $warp = strtolower($args[0]);
            if (!isset(Main::getInstance()->warps[strtolower($warp)])) {
                $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That warp was not found!");
                $warpString = "";
                foreach (Main::getInstance()->warps as $name => $data) {
                    if (!isset($data["op"])) {
                        $warpString .= $name . ", ";
                    }
                }
                $warpString = substr($warpString, 0, -2);
                $this->sendMessage($sender, TextFormat::YELLOW . "The available warps are: $warpString");
                return;
            }
            Main::getInstance()->getFormFunctions()->sendWarpsConfirm($sender, $warp);
        }
    }
}