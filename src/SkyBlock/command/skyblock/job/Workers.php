<?php


namespace SkyBlock\command\skyblock\job;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class Workers extends BaseSkyblock {

    const ON = '§a[ON]';
    const OFF = '§c[OFF]';

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'workers', "Shows all workers of your island with jobs.", ['ourworkers', 'roles', 'ourroles', 'jobs']);
    }

    public function execute(Player $sender, User $user, array $args) : void {

        if (!isset($args[1])) {
            if (!$user->isIslandSet()) {
                $this->sendMessage($sender, "§4[Error]§e You do not own any island to see members of, §cuse /is workers <island name>");
                return;
            }
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error]§c Island not online");
                return;
            }
            $str = "§a{$islandName} §6Island Workers §7[§f" . $island->getRoleCount() . "§7/§f" . $island->getRoleLimit() . "§7] -\n";
            foreach ($island->getJobs() as $job) {
                $jobname = ucfirst($job);
                $str .= "§b$jobname :\n";
                $this->sendCommandMessage($island->$job, $str);
            }
        } else {
            $islandName = $args[1];
            if (!$this->db->isNameUsed($islandName)) {
                $this->sendMessage($sender, "§4[Error] §cIsland not found!");
                return;
            }
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $data = $this->db->getIslandInfo4Data($islandName);
                unset($data["name"]);
                $str = "§a{$islandName} §6Island Workers -\n";
                foreach ($data as $job => $jstr) {
                    $jobname = ucfirst($job);
                    $str .= "§b$jobname :\n";
                    $workers = [];
                    if ($jstr != "") $workers = explode(",", $jstr);
                    $this->sendCommandMessage($workers, $str);
                }
            } else {
                $str = "§a{$islandName} §6Island Workers §7[§f" . $island->getRoleCount() . "§7/§f" . $island->getRoleLimit() . "§7] -\n";
                foreach ($island->getJobs() as $job) {
                    $jobname = ucfirst($job);
                    $str .= "§b$jobname :\n";
                    $this->sendCommandMessage($island->$job, $str);
                }
            }
        }
        $this->sendMessage($sender, $str);
    }

    /**
     * @param array  $workers
     * @param string $str
     */
    public function sendCommandMessage(array $workers, string &$str) : void {
        $str .= "- ";
        $i = 1;
        foreach ($workers as $worker) {
            if ($this->um->getOnlineUser($worker) !== null) {
                $str .= "§f" . $i . ". §f$worker " . self::ON . ", ";
            } else {
                $str .= "§f" . $i . ". §f$worker " . self::OFF . ", ";
            }
            $i++;
        }
        $str = substr($str, 0, -2) . "\n";
    }

}