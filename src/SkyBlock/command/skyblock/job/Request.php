<?php


namespace SkyBlock\command\skyblock\job;


use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\perms\Permission;
use SkyBlock\user\User;

class Request extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'request', 'Request a player to get a job on your island', ['req', 'job', 'hire']);
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou need to be Island Owner/Coowner to use that command!");
            return;
        }
        $islandName = $user->getIsland();
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§4[Error]§c Island not online");
            return;
        }
        if (!$island->hasPerm($sender->getName(), Permission::MANAGER)) {
            $this->sendMessage($sender, TextFormat::RED . "You dont have managing perms on this island");
            return;
        }
        if (!isset($args[2])) {
            $this->sendMessage($sender, "§cUsage: /is hire <job> <player>\n§6Available jobs - §e" . $island->getJobsTrimmed());
            return;
        }
        $job = strtolower($args[1]);
        if (!$island->isJob($job . "s")) {
            $this->sendMessage($sender, "§4[Error] §c{$job} job not found! §6Available jobs - §e" . $island->getJobsTrimmed());
            return;
        }
        $playerName = strtolower($args[2]);
        $player = $this->pl->getServer()->getPlayerByPrefix($playerName);
        if (!$player instanceof Player) {
            $this->sendMessage($sender, "§4[Error] §c{$args[2]} is not online!");
            return;
        }
        if (strtolower($player->getName()) == strtolower($sender->getName())) {
            $this->sendMessage($sender, "§cYou cannot request yourself!");
            return;
        }
        if ($island->isBanned($player->getName())) {
            $this->sendMessage($sender, "§cThat player was banned from this island by Owner!");
            return;
        }
        if ($island->hasARole($player->getName())) {
            $this->sendMessage($sender, "§cPlayer already has a role on your island! §6Use /is fire to remove role first before inviting.");
            return;
        }
        $user2 = $this->um->getOnlineUser(strtolower($player->getName()));
        if ($user2 === null) {
            $this->sendMessage($sender, "§4[Error] §c{$args[2]} is not online!");
            return;
        }
        $limit = $island->getRoleLimit();
        if ($island->getRoleCount() >= $limit) {
            $this->sendMessage($sender, "§4[Error] §cYou can only have {$limit} jobs on your island, increase your island level by mining or building to get more job slots!");
            return;
        }
        if (strtolower($islandName) == strtolower($user2->getIsland())) {
            $this->sendMessage($sender, "§4[Error] §cThat player is a member of the island!");
            return;
        }
        if ($island->isHelper(strtolower($player->getName()))) {
            $this->sendMessage($sender, "§4[Error] §cThat player is a helper on your island!");
            return;
        }
        if (isset($this->pl->requests[strtolower($sender->getName())][strtolower($player->getName())])) {
            $time = $this->pl->requests[strtolower($sender->getName())][strtolower($player->getName())]["time"];
            $now = time();
            if (($now - $time) <= 60) {
                $this->sendMessage($sender, "§4[Error] §cYou've already sent a job request to that player! Wait till it gets timed out or till they respond!");
                return;
            } else unset($this->pl->requests[strtolower($sender->getName())][strtolower($player->getName())]);
        }
        $this->pl->requests[strtolower($sender->getName())][strtolower($player->getName())]["time"] = time();
        $this->pl->requests[strtolower($sender->getName())][strtolower($player->getName())]["island"] = $islandName;
        $this->pl->requests[strtolower($sender->getName())][strtolower($player->getName())]["job"] = $job;
        $this->sendMessage($sender, "§aYou sent a §e`{$job}` §ajob request to §b{$player->getName()} §asuccessfully. §f" . $island->getJobDesc($job . "s") . ", if accepted!");
        $this->sendMessage($player, "§b{$sender->getName()} §ahas sent you a §e`{$job}` §ajob request on their island §e$islandName §a! §2Do /is sign {$sender->getName()} §ato accept their request, or §2/is ignore {$sender->getName()} §ato deny their request.");
    }

}