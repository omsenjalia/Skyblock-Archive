<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class Kick extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'kick', 'Kick someone from your island');
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be Island Owner/Cooowner to use that command!");
        } else {
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error] §cIsland not online!");
                return;
            }
            if (!isset($args[1])) {
                $this->sendMessage($sender, "§cUsage: /is kick <player> or /is expel <player>");
                return;
            }
            $player = $this->plugin->getServer()->getPlayerByPrefix($args[1]);
            if ($player instanceof Player and $player->isOnline()) {
                if (strtolower($player->getName()) == strtolower($sender->getName())) {
                    $this->sendMessage($sender, "§4[Error] §cYou cannot kick yourself from the island!");
                    return;
                }
                $level = $island->getId();
                if ($player->getPosition()->getWorld()->getDisplayName() != $level) {
                    $this->sendMessage($sender, "§4[Error] §cThat player isn't on your island! If you meant removing from helper list, use /is remove <helper>");
                    return;
                }
                if ($island->isAnOwner($player->getName())) {
                    $this->sendMessage($sender, "§4[Error] §cCan't kick island Owner/CoOwners!");
                    return;
                }
                if ($this->pl->staffapi->isSoftStaff($player->getName())) {
                    $this->sendMessage($sender, "§4[Error] §cCan't kick staff!");
                    return;
                }
                $this->plugin->teleportToSpawn($player);
                $this->sendMessage($sender, "{$player->getName()} §chas been kicked from your island to the server spawn! Do /is lock to lock your island so players cant join! Do /is remove to remove a helper!");
                if (strtolower($sender->getName()) !== strtolower($island->getOwner())) {
                    if (($owner = $this->um->getOnlineUser($island->getOwner())) !== null) {
                        $this->sendMessage($owner->getPlayer(), "§e{$player->getName()} was kicked to spawn from your Island.\n§6Kicked by CoOwner - §a{$sender->getName()}");
                    }
                }
            } else {
                $this->sendMessage($sender, "§4[Error] §cPlayer not online!");
            }
        }
    }

}