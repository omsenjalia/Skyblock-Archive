<?php


namespace SkyBlock\command\home;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

/**
 * @deprecated
 * */
class DelHome extends BaseCommand {

    /**
     * DelHome constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "delhome", "Delete a set home", "<home name>");
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
            $this->sendMessage($sender, "§cUsage: /delhome <home name>");
            return;
        }
        $homename = $args[0];
        $user = $this->um->getOnlineUser($sender->getName());
        if (!$user->hasHome($homename)) {
            $this->sendMessage($sender, "§4[Error]§c You haven't set that home! Use /sethome to set homes!");
            return;
        }
        $user->removeHome($homename);
        $this->sendMessage($sender, "§eNether Home §a{$homename} §edeleted!");
    }

}