<?php


namespace SkyBlock\command\skyblock\helper;


use pocketmine\player\Player;
use SkyBlock\command\skyblock\BaseSkyblock;
use SkyBlock\Main;
use SkyBlock\user\User;

class Helpers extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'helpers', "Shows all members of your island.", ['members', 'coowners', 'ourcoowners', 'admins', 'ouradmins', 'adminsof', 'officers', 'ourmembers', 'membersof']);
    }

    public function execute(Player $sender, User $user, array $args) {
        $on = '§a[ON]';
        $off = '§c[OFF]';
        if (!isset($args[1])) {
            if (!$user->isIslandSet()) {
                $this->sendMessage($sender, "§4[Error]§e You do not own any island to see members of, §cuse /is members <island name>");
                return;
            }
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error]§c Island not online");
                return;
            }
            $str = "§a{$islandName} §2Island Members -\n";
            if ($this->um->getOnlineUser($island->getOwner()) !== null) {
                $str .= "§f- {$island->getOwner()} §2[§eOwner§2] $on \n";
            } else {
                $str .= "§f- {$island->getOwner()} §2[§eOwner§2] $off \n";
            }
            $i = 1;
            $helper = $island->getHelpers();
            foreach ($helper as $member) {
                if ($this->um->getOnlineUser($member) !== null) {
                    $str .= "§f{$i}. §f{$member} §3[{$island->getPlayerRank($member)}] {$on}\n";
                } else {
                    $str .= "§f{$i}. §f{$member} §3[{$island->getPlayerRank($member)}] {$off}\n";
                }
                ++$i;
            }
        } else {
            $islandName = $args[1];
            if (!$this->db->isNameUsed($islandName)) {
                $this->sendMessage($sender, "§4[Error] §cIsland not found!");
                return;
            }
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $data = $this->db->getIslandInfoData($islandName);
                $owner = $data['owner'];
                $helper = $data['helpers'];
                $helpers = explode(",", $helper);
                $admin = $data['admins'];
                $admins = explode(",", $admin);
                $coowner = $data['coowners'];
                $coowners = explode(",", $coowner);
                $i = 1;
                $str = "§a{$islandName} §2Island Members -\n";
                $str .= "§f- $owner §3[§eOwner§3] $off \n";
                foreach ($helpers as $member) {
                    if (in_array($member, $coowners, true)) $rank = "[CoOwner]";
                    elseif (in_array($member, $admins, true)) $rank = "[Admin]";
                    else    $rank = "[Helper]";
                    if ($this->um->getOnlineUser($member) !== null) {
                        $str .= "§f{$i}. §f{$member} §3{$rank} {$on}\n";
                    } else {
                        $str .= "§f{$i}. §f{$member} §3{$rank} {$off}\n";
                    }
                    ++$i;
                }
            } else {
                $str = "§a{$island->getName()} §2Island Members -\n";
                $owner = $island->getOwner();
                if ($this->um->getOnlineUser($owner) !== null) {
                    $str .= "§f- {$owner} §3[§eOwner§3] $on \n";
                } else {
                    $str .= "§f- {$owner} §3[§eOwner§3] $off \n";
                }
                $i = 1;
                $helper = $island->getHelpers();
                foreach ($helper as $member) {
                    if ($this->um->getOnlineUser($member) !== null) {
                        $str .= "§f{$i}. §f{$member} §3{$island->getPlayerRank($member)} {$on}\n";
                    } else {
                        $str .= "§f{$i}. §f{$member} §3{$island->getPlayerRank($member)} {$off}\n";
                    }
                    ++$i;
                }
            }
        }
        $this->sendMessage($sender, $str);
    }

}