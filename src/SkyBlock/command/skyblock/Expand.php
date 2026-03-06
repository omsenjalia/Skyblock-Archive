<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class Expand extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'expand', "Increase your Island's capabilities (radius, limits etc.)");
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!$user->isIslandSet()) {
            $this->sendMessage($sender, "§4[Error] §cYou must be the island owner/coowner to expand your island!");
        } else {
            $islandName = $user->getIsland();
            if (($island = $this->im->getOnlineIsland($islandName)) === null) {
                $this->sendMessage($sender, "§4[Error] §cIsland not online!");
                return;
            }
            $radius = $island->getRadius();
            $newradius = $radius + 10;
            if ($radius >= $island->getRadiusMax()) {
                $this->sendMessage($sender, "§4[Error] §cYour island is at the max radius $radius You can't expand anymore!");
                return;
            }
            $reqlevel = ($newradius / 2) + 5;
            $reqmoney = $reqlevel * 10000;
            if ($island->getLevel() < $reqlevel) {
                $this->sendMessage($sender, "§4[Error] §cYour island is not at the required level for that yet! §eRequired Level: §d$reqlevel §eRequired Money: §6$reqmoney$\n=> Increase Island level by Building or Mining on your island!");
                return;
            }
            if (!$island->removeMoney($reqmoney)) {
                $this->sendMessage($sender, "§4[Error] §cYour island bank does not have the required money for that! §eRequired Money: §6$reqmoney$\n§e=> Add Money in island bank by /is donate Ask your island helpers to help!");
                return;
            }
            $island->expandRadius();
            $this->sendMessage($sender, "§eYou successfully expanded your Island §a{$island->getName()} §efor §6$reqmoney$!\n§6New Radius - §f{$newradius}§7/§f{$island->getRadiusMax()}\n§6New Helper Limit - §f{$island->getHelperLimit()}§7/§f{$island->getHelperMax()}\n§6New Spawner Limit - §f{$island->getSpawnerLimit()}§7/§f{$island->getSpawnerMax()}\n§6New AutoMiner Limit - §f{$island->getAutoMinerLimit()}§7/§f{$island->getAutoMinerMax()}\n§6New AutoSeller Limit - §f{$island->getAutoSellerLimit()}§7/§f{$island->getAutoSellerMax()}\n§6New OreGen Limit - §f{$island->getCatalystLimit()}§7/§f{$island->getCatalystMax()}\n§6New Hopper Limit - §f{$island->getHopperLimit()}§7/§f{$island->getHopperMax()}\n§6New Farm Limit - §f{$island->getFarmLimit()}§7/§f{$island->getFarmMax()}");
            if (strtolower($sender->getName()) != strtolower($island->getOwner())) {
                if (($owner = $this->um->getOnlineUser($island->getOwner())) !== null) {
                    $this->sendMessage($owner->getPlayer(), "§eSuccessfully expanded your Island §a{$island->getName()} §efor §6$reqmoney$!\n§6New Radius - §f{$newradius}§7/§f{$island->getRadiusMax()}\n§6New Helper Limit - §f{$island->getHelperLimit()}§7/§f{$island->getHelperMax()}\n§6New Spawner Limit - §f{$island->getSpawnerLimit()}§7/§f{$island->getSpawnerMax()}\n§6New AutoMiner Limit - §f{$island->getAutoMinerLimit()}§7/§f{$island->getAutoMinerMax()}}\n§6New AutoSeller Limit - §f{$island->getAutoSellerLimit()}§7/§f{$island->getAutoSellerMax()}\n§6New OreGen Limit - §f{$island->getCatalystLimit()}§7/§f{$island->getCatalystMax()}\n§6New Hopper Limit - §f{$island->getHopperLimit()}§7/§f{$island->getHopperMax()}\n§6New Farm Limit - §f{$island->getFarmLimit()}§7/§f{$island->getFarmMax()}\n§6Expanded by CoOwner - §a{$sender->getName()}");
                }
            }
        }
    }

}