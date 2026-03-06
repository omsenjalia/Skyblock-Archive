<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class TopWins extends BaseCommand {
    /**
     * TopWins constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'topwins', 'Check topwins of casino');
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!isset($args[0])) {
            $args[0] = 1;
        }
        if (!is_int((int) $args[0]) || $args[0] <= 0) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Enter a valid page number!");
            return;
        }
        $page = (int) $args[0];
        $array = Main::getInstance()->db->prepare("SELECT COUNT(*) AS COUNT FROM player ORDER BY won DESC;")->execute();
        $array = $array->fetchArray(SQLITE3_ASSOC);
        $total = $array["count"];
        $pages = ceil($total / 8);
        if ($pages < $page) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " That page cannot be found. Last page is $pages");
            return;
        }
        $startNumber = ($page - 1) * 8;
        $this->sendMessage($sender, TextFormat::YELLOW . "Top Casino Wins List -");
        $string = TextFormat::YELLOW . "[+]" . TextFormat::WHITE . str_repeat("=", 10) . TextFormat::AQUA . "[ " . $page . "/" . $pages . " ]" . TextFormat::WHITE . str_repeat("=", 10) . TextFormat::YELLOW . "[+]\n";
        $array = Main::getInstance()->db->prepare("SELECT player, won FROM player ORDER BY won DESC LIMIT $startNumber, 8;")->execute();
        $result = $array->fetchArray(SQLITE3_ASSOC);
        while ($result) {
            $won = $result["won"];
            $name = $result["player"];
            $startNumber++;
            $user = Main::getInstance()->getUserManager()->getOnlineUser($name);
            if ($user === null) {
                $string .= TextFormat::WHITE . "$startNumber. $name => $" . number_format($won) . " \n";
            } else {
                $string .= TextFormat::WHITE . "$startNumber. $name => $" . number_format($user->getWon()) . " \n";
            }
        }
        $this->sendMessage($sender, $string . TextFormat::YELLOW . "[+]" . TextFormat::WHITE . str_repeat("=", 26) . TextFormat::YELLOW . "[+]\n=> Pages will be reloaded after restart! <=");
    }

}