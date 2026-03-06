<?php


namespace SkyBlock\command\home;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

/**
 * @deprecated
 * */
class Homes extends BaseCommand {

    /**
     * Homes constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "homes", "See your homes");
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
        if (isset($args[0])) {
            $this->sendMessage($sender, "§cUsage: /homes");
            return;
        }
        $user = $this->um->getOnlineUser($sender->getName());
        if (!$user->hasHomes()) {
            $this->sendMessage($sender, "§4[Error]§c No homes found");
            return;
        }
        $this->sendMessage($sender, "§eAvailable Homes: §a{$user->getHomesString()}");
    }

}