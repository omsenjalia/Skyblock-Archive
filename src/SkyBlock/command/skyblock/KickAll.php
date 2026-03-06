<?php


namespace SkyBlock\command\skyblock;

use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class KickAll extends BaseSkyblock {

    public const MAX_TIMER = 10; // seconds

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'kickall', 'Kick everyone from your island');
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
            if (!isset($args[1]) or isset($args[2])) {
                $this->sendMessage($sender, "§cUsage: /is kickall <yes | no> §7| §6Enter yes if you want to kick helpers too from your Island to server spawn");
                return;
            }
            $flag = strtolower($args[1]);
            if ($flag !== "yes" && $flag !== "no") {
                $this->sendMessage($sender, "§cUsage: /is kickall <yes | no> §7| §6Enter yes if you want to kick helpers too from your Island to server spawn");
                return;
            }
            $kickhelpers = false;
            if ($flag === "yes") $kickhelpers = true;
            if (isset($this->pl->kickalltimer[$islandName]) && time() < $this->pl->kickalltimer[$islandName]) {
                $left = $this->pl->kickalltimer[$islandName] - time();
                $this->sendMessage($sender, "§4[Error] §cPlease wait §4$left §cseconds to use this command again for your island!");
                return;
            }
            $level = $island->getWorldLevel();
            foreach ($level->getPlayers() as $p) {
                if (strtolower($sender->getName()) === strtolower($p->getName())) continue; // sender
                elseif (($user = $this->um->getOnlineUser($p->getName())) === null) continue; // not online
                elseif ($user->getIsland() === $islandName) continue; // coowner
                elseif ($this->pl->staffapi->isSoftStaff($p->getName())) continue; // staff
                elseif (!$kickhelpers && $island->isHelper($p->getName())) continue; // helper
                $this->plugin->teleportToSpawn($p);
            }
            $this->pl->kickalltimer[$islandName] = time() + self::MAX_TIMER;
            $this->sendMessage($sender, "§cEveryone has been kicked from your island to the server spawn, do /is lock to lock your island so players cant join");
            if (strtolower($sender->getName()) !== strtolower($island->getOwner())) {
                if (($owner = $this->um->getOnlineUser($island->getOwner())) !== null) {
                    $this->sendMessage($owner->getPlayer(), "§eEveryone was kicked to spawn from your Island.\n§6Kicked by CoOwner - §a{$sender->getName()}");
                }
            }
        }
    }

}