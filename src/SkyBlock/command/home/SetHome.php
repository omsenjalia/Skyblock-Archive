<?php


namespace SkyBlock\command\home;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\Values;

/**
 * @deprecated
 * */
class SetHome extends BaseCommand {

    /**
     * SetHome constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "sethome", "Set a home", "<home name>");
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendUsage($sender, $commandLabel);
            return;
        }
        if (!isset($args[0]) or isset($args[1])) {
            $this->sendMessage($sender, "§cUsage: /sethome <home name>");
            return;
        }
        if ($sender->getPosition()->getWorld()->getDisplayName() !== Values::NETHER_WORLD) {
            $this->sendMessage($sender, "§4[Error]§c You cannot create homes here! Only in Nether world");
            return;
        }
        $homename = $args[0];
        if (!ctype_alnum($homename)) {
            $this->sendMessage($sender, "§4[Error] §cHome names can only include letters or numbers. No spaces.");
            return;
        }
        $len = strlen($homename);
        if ($len < 2 or $len > 15) {
            $this->sendMessage($sender, "§4[Error] §cName needs to be longer than 1 and smaller than 16 characters!");
            return;
        }
        $limit = Values::MAX_USER_HOME_LIMIT;
        $user = $this->um->getOnlineUser($sender->getName());
        if (!$user->hasHome($homename) and $user->getHomesCount() >= $limit) {
            $this->sendMessage($sender, "§4[Error]§c You can only have $limit homes! Try deleting homes by /delhome or updating existing homes by /sethome <old home>");
            return;
        }
        $status = ($user->hasHome($homename)) ? "§6updated" : "§ecreated";
        $this->sendMessage($sender, "§eNether Home §a{$homename} $status §eat §7X: §b{$sender->getPosition()->getFloorX()} §7Y: §b{$sender->getPosition()->getFloorY()} §7Z: §b{$sender->getPosition()->getFloorZ()}");
        $user->updateHome($homename, $sender->getLocation());
    }

}