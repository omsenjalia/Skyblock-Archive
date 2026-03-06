<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class Info extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'info', "Shows all info of the island.");
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!isset($args[1])) {
            if (!$user->isIslandSet()) {
                $this->sendMessage($sender, "§cUsage: /is info <island name>");
                return;
            }
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error]§c Island not online");
                return;
            }
            $sender->sendMessage("==================== SKYBLOCK ISLAND ====================\n§fIsland: §a$islandName\n§fOwner: §b{$island->getOwner()}     §fCreator: §a{$island->getCreator()}\n§fState: §b{$island->getLockedState()}\n§fLevel: §d{$island->getLevel()} §fPoints: §6{$island->getPoints()}§f/§6{$island->getPointsNeeded()}\n§fBank: §6{$island->getMoney()}$" . "§7/§6{$island->getBankLimit()}$\n§fCoOwners§7[§6{$island->getCoownerCount()}§7/§6{$island->getCoownerLimit()}§7]: §b{$island->getCoownerString()}\n§fAdmins§7[§6{$island->getAdminCount()}§7]: §9{$island->getAdminString()}\n§fHelpers§7[§6{$island->getHelperCount()}§7/§6{$island->getHelperLimit()}§7]: §e{$island->getOnlyHelperString()}\n§fHomes§7[§6{$island->getHomesCount()}§7/§6{$island->getHomesLimit()}§7]: §e{$island->getHomesString()}\n§fRadius: §3{$island->getRadius()} Blocks\n§fAutoMiner Receiver: §a{$island->getReceiver()}\n§fMotd: §b{$island->getMotd()}\n§fStatus: §a'[ON]'\n§fBlocks: §eCatalyst: §f{$island->getCatalyst()}/{$island->getCatalystLimit()} §eAutoMiner: §f{$island->getAutoMiner()}/{$island->getAutoMinerLimit()} §eAutoSeller: §f{$island->getAutoSeller()}/{$island->getAutoSellerLimit()} §eSpawner: §f{$island->getSpawner()}/{$island->getSpawnerLimit()} §eHopper: §f{$island->getHopper()}/{$island->getHopperLimit()} §eFarm: §f{$island->getFarm()}/{$island->getFarmLimit()}\n§fVisitor Limit: §a{$island->getVLimit()}\n§f=========================================================");
            return;
        }
        if (!$this->db->isNameUsed($args[1])) {
            $this->sendMessage($sender, "§4[Error] §cIsland not found!");
            return;
        }
        if (($island = $this->im->getOnlineIsland($args[1])) !== null) {
            $sender->sendMessage("==================== SKYBLOCK ISLAND ====================\n§fIsland: §a{$args[1]}\n§fOwner: §b{$island->getOwner()}     §fCreator: §a{$island->getCreator()}\n§fState: §b{$island->getLockedState()}\n§fLevel: §d{$island->getLevel()} §fPoints: §6{$island->getPoints()}§f/§6{$island->getPointsNeeded()}\n§fBank: §6{$island->getMoney()}$" . "§7/§6{$island->getBankLimit()}$\n§fCoOwners§7[§6{$island->getCoownerCount()}§7/§6{$island->getCoownerLimit()}§7]: §b{$island->getCoownerString()}\n§fAdmins§7[§6{$island->getAdminCount()}§7]: §9{$island->getAdminString()}\n§fHelpers§7[§6{$island->getHelperCount()}§7/§6{$island->getHelperLimit()}§7]: §e{$island->getOnlyHelperString()}\n§fHomes§7[§6{$island->getHomesCount()}§7/§6{$island->getHomesLimit()}§7]: §e{$island->getHomesString()}\n§fRadius: §3{$island->getRadius()} Blocks\n§fAutoMiner Receiver: §a{$island->getReceiver()}\n§fMotd: §b{$island->getMotd()}\n§fStatus: §a'[ON]'\n§fBlocks: §eCatalyst: §f{$island->getCatalyst()}/{$island->getCatalystLimit()} §eAutoMiner: §f{$island->getAutoMiner()}/{$island->getAutoMinerLimit()} §eAutoSeller: §f{$island->getAutoSeller()}/{$island->getAutoSellerLimit()} §eSpawner: §f{$island->getSpawner()}/{$island->getSpawnerLimit()} §eHopper: §f{$island->getHopper()}/{$island->getHopperLimit()} §eFarm: §f{$island->getFarm()}/{$island->getFarmLimit()}\n§fVisitor Limit: §a{$island->getVLimit()}\n§f=========================================================");
        } else {
            $level = ($levdata = $this->db->getIslandLevelData($args[1]))["level"];
            $points = $levdata["points"];
            $pneeded = $level * 150;
            $data = $this->db->getIslandInfoData($args[1]);
            $data2 = $this->db->getIslandInfo2Data($args[1]);
            $creator = $data2["creator"];
            $owner = $data['owner'];
            $receiver = ($data['receiver'] === "") ? $owner : $data['receiver'];
            $state = $this->db->getIslandLocked($args[1]);
            if ($state == 'true') $state = "Locked";
            else    $state = "Unlocked";
            $money = $this->db->getIslandMoney($args[1]);
            $radius = $this->db->getIslandRadius($args[1]);
            $motd = $this->db->getIslandMotd($args[1]);
            $blimit = $level * 25000;
            $coowners = $data['coowners'];
            $coowner = [];
            if ($coowners != "") {
                $coowner = explode(",", $coowners);
                $ccount = count($coowner);
            } else $ccount = 0;
            $admins = $data['admins'];
            $admin = [];
            if ($admins != "") {
                $admin = explode(",", $admins);
                $acount = count($admin);
            } else $acount = 0;
            $helpers = $data['helpers'];
            $hstr = "";
            if ($helpers != "") {
                $helper = explode(",", $helpers);
                $string = "";
                foreach ($helper as $h) {
                    if (!in_array($h, $admin, true) and !in_array($h, $coowner, true)) $string .= $h . ",";
                }
                $hstr = substr($string, 0, -1);
                $count = count($helper);
            } else $count = 0;
            $hlimit = (int) (($level / 5) + 3);
            if ($hlimit > 30) $hlimit = 30;
            $climit = (int) ($level / 30);
            if ($climit > 15) $climit = 15;
            $sender->sendMessage("==================== SKYBLOCK ISLAND ====================\n§fIsland: §a{$args[1]}\n§fOwner: §b{$owner}     §fCreator: §a{$creator}\n§fState: §b{$state}\n§fLevel: §d{$level} §fPoints: §6{$points}§f/§6{$pneeded}\n§fBank: §6{$money}$" . "§7/§6{$blimit}$\n§fCoOwners§7[§6{$ccount}§7/§6{$climit}§7]: §b{$coowners}\n§fAdmins§7[§6{$acount}§7]: §9{$admins}\n§fHelpers§7[§6{$count}§7/§6{$hlimit}§7]: §e{$hstr}\n§fRadius: §3{$radius} Blocks\n§fAutoMiner Receiver: §a{$receiver}\n§fMotd: §b{$motd}\n§fStatus: §c'[OFF]'\n§f=========================================================");
        }
    }

}