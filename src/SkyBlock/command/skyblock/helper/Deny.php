<?php


namespace SkyBlock\command\skyblock\helper;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class Deny extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'deny', "", ['reject', 'no']);
    }

    public function execute(Player $sender, User $user, array $args) {
        if (isset($args[1])) {
            $player = $this->plugin->getServer()->getPlayerByPrefix(strtolower($args[1]));
            if ($player instanceof Player and $player->isOnline()) {
                $playerName = strtolower($player->getName());
                if (!isset($this->pl->invitations[$playerName][strtolower($sender->getName())])) {
                    $this->sendMessage($sender, "§4[Error] §cYou haven't received any island requests from §a$playerName");
                    return;
                }
                $this->sendMessage($player, "§a{$sender->getName()} §cdenied your island invite!");
                $this->sendMessage($sender, "§eIsland invite request denied!");
                unset($this->pl->invitations[$playerName][strtolower($sender->getName())]);
            } else {
                $this->sendMessage($sender, "§4[Error] §c{$args[1]} is not online anymore!");
            }
        } else {
            $this->sendMessage($sender, "§cUsage: /is deny <player> or /is reject <player> or /is no <player>");
        }
    }

}