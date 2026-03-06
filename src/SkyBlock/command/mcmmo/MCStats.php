<?php


namespace SkyBlock\command\mcmmo;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class MCStats extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'mcstats');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendUsage($sender, $commandLabel);
            return false;
        }
        $user = $this->um->getOnlineUser($sender->getName());
        if (!isset($args[0])) {
            $stats = $user->getBase();
            $str = TextFormat::GOLD . "-=MCMMO STATS FOR §a" . $sender->getName() . "§6 =-\n";
            foreach ($stats as $type => $data) {
                $str .= TextFormat::YELLOW . ucfirst($type) . ": §9Level: " . TextFormat::GREEN . $data["level"] . TextFormat::BLUE . " XP(" . TextFormat::GRAY . $data["exp"] . TextFormat::BLUE . "/" . TextFormat::GRAY . $data["level"] * 100 . TextFormat::BLUE . ")\n";
            }
            $this->sendMessage($sender, $str . TextFormat::GOLD . "=> Use /mcstats <player> to see other player's mcstats!");
        } else {
            if (!$this->db->isPlayerRegistered($args[0])) {
                $this->sendMessage($sender, "§4[Error]§c That player never connected!");
                return false;
            }
            if (($user = $this->um->getOnlineUser($args[0])) === null) {
                $stats = $this->db->getPlayerBase($args[0]);
                $str = TextFormat::GOLD . "-=MCMMO STATS FOR §a" . $args[0] . "§6 =-\n";
                foreach ($stats as $type => $data) {
                    $str .= TextFormat::YELLOW . ucfirst($type) . ": §9Level: " . TextFormat::GREEN . $data["level"] . TextFormat::BLUE . " XP(" . TextFormat::GRAY . $data["exp"] . TextFormat::BLUE . "/" . TextFormat::GRAY . $data["level"] * 100 . TextFormat::BLUE . ")\n";
                }
                $this->sendMessage($sender, $str);
            } else {
                $stats = $user->getBase();
                $str = TextFormat::GOLD . "-=MCMMO STATS FOR §a" . $user->getName() . "§6 =-\n";
                foreach ($stats as $type => $data) {
                    $str .= TextFormat::YELLOW . ucfirst($type) . ": §9Level: " . TextFormat::GREEN . $data["level"] . TextFormat::BLUE . " XP(" . TextFormat::GRAY . $data["exp"] . TextFormat::BLUE . "/" . TextFormat::GRAY . $data["level"] * 100 . TextFormat::BLUE . ")\n";
                }
                $this->sendMessage($sender, $str);
            }
        }
        return true;
    }
}