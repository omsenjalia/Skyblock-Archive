<?php


namespace SkyBlock\command\mcmmo;


use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class MCTop extends BaseCommand {
    /**
     * MCTop constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'mctop', 'Check MCMMO Top players');
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!isset($args[0])) {
            $this->sendMessage($sender, "§cUsage: /mctop <type> \n§6=> §aTypes: §eCombat§6, §eFarming§6, §eMining§6, §eGambling");
            return;
        }
        $type = strtolower($args[0]);
        if ($type != "combat" && $type != "farming" && $type != "mining" && $type != "gambling") {
            $this->sendMessage($sender, "§4[Error]§c Type not valid! §cUsage: /mctop <type> \n§6=> §aTypes: §eCombat§6, §eFarming§6, §eMining§6, §eGambling");
            return;
        }
        $str = TF::GREEN . "Top 10 MCMMO players for $type -\n";
        $i = 1;
        $result = $this->pl->db->prepare("SELECT player, level, exp FROM {$type} ORDER BY level DESC LIMIT 10;")->execute();
        while ($array = $result->fetchArray(SQLITE3_ASSOC)) {
            if (($user = $this->um->getOnlineUser($array["player"])) === null) {
                $str .= $i . ". " . TextFormat::YELLOW . ucfirst($array["player"]) . ": §9Level: " . TextFormat::GREEN . $array["level"] . TextFormat::BLUE . " XP(" . TextFormat::GRAY . $array["exp"] . TextFormat::BLUE . "/" . TextFormat::GRAY . $array["level"] * 100 . TextFormat::BLUE . ")\n";
            } else {
                $str .= $i . ". " . TextFormat::YELLOW . ucfirst($array["player"]) . ": §9Level: " . TextFormat::GREEN . $user->getLevel($type) . TextFormat::BLUE . " XP(" . TextFormat::GRAY . $user->getPoints($type) . TextFormat::BLUE . "/" . TextFormat::GRAY . $user->getPointsNeeded($type) . TextFormat::BLUE . ")\n";
            }
            ++$i;
        }
        $this->sendMessage($sender, $str . "§r§f=> Pages will be reloaded after restart! <=");
    }
}