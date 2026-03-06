<?php


namespace SkyBlock\command\skyblock\job;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\island\Island;
use SkyBlock\Main;
use SkyBlock\user\User;

class FireAll extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'fireall', "Fire all workers together from your island");
    }

    public function execute(Player $sender, User $user, array $args) {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou must be an island owner to use this command!");
            return;
        }
        $islandName = $user->getIsland();
        if (($island = $this->im->getOnlineIsland($islandName)) === null) {
            $this->sendMessage($sender, "§4[Error]§c Island not online");
            return;
        }
        if (isset($args[1])) {
            $job = strtolower($args[1]);
            $jobname = ucfirst($job);
            if (!$island->isJob($job)) {
                $this->sendMessage($sender, "§4[Error] §c{$jobname} job not found! §6Available jobs - §e" . $island->getJobsTrimmed(true));
                return;
            }
            if (!isset($this->pl->fireallconfirm[$sender->getName()])) {
                $this->pl->fireallconfirm[$sender->getName()] = true;
                $this->sendMessage($sender, "§3Are you sure you want to fire all the $jobname workers from your island?\n§c> Run the command again to confirm");
                return;
            }
            unset($this->pl->fireallconfirm[$sender->getName()]);
            $this->fireWorkers($island, $job);
            $this->sendMessage($sender, "§eFired all workers from the Island successfully!");
        } else {
            if (!isset($this->pl->fireallconfirm[$sender->getName()])) {
                $this->pl->fireallconfirm[$sender->getName()] = true;
                $this->sendMessage($sender, "§3Are you sure you want to fire all the workers from your island?\n§c> Run the command again to confirm");
                return;
            }
            unset($this->pl->fireallconfirm[$sender->getName()]);
            $jobs = $island->getJobs();
            foreach ($jobs as $job) {
                $this->fireWorkers($island, $job);
            }
            $this->sendMessage($sender, "§eFired all workers from the Island successfully!");
        }
    }

    public function fireWorkers(Island $island, string $job) {
        $workers = $island->$job;
        $jobname = ucfirst($job);
        if (!empty($workers)) {
            foreach ($workers as $worker) {
                if (($user2 = $this->um->getOnlineUser($worker)) !== null) {
                    if (strtolower($user2->getPlayer()->getPosition()->getWorld()->getDisplayName()) === $island->getId())
                        $this->plugin->teleportToSpawn($user2->getPlayer());
                    $this->sendMessage($user2->getPlayer(), "§cYou have been fired from your §6{$jobname} §cjob at island {$island->getName()}!");
                }
            }
            $island->$job = [];
        }
    }

}