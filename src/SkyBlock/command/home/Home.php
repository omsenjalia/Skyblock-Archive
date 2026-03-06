<?php


namespace SkyBlock\command\home;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\Util;
use SkyBlock\util\Values;

/**
 * @deprecated
 * */
class Home extends BaseCommand {

    /**
     * Home constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, "home", "Teleport to your home", "<home name>");
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
            $this->sendMessage($sender, "§cUsage: /home <home name>");
            return;
        }
        if ($sender->getWorld()->getDisplayName() === Values::PVP_WORLD) {
            $this->sendMessage($sender, "§4[Error]§c You cannot teleport to your nether home from here!");
            return;
        }
        $homename = $args[0];
        $user = $this->um->getOnlineUser($sender->getName());
        if (!$user->hasHome($homename)) {
            $this->sendMessage($sender, "§4[Error]§c You haven't set that home! Use /sethome to set homes!");
            return;
        }
        $user->teleportToHome($sender, $homename);
        if ($this->pl->netherReset > time()) {
            $reset = "in " . Util::getTimePlayed($this->pl->netherReset - time());
        } else {
            $reset = "next restart!";
        }
        $this->sendMessage($sender, "§eTeleport Successful!\n§cNether world will auto reset §7" . $reset);
    }

}