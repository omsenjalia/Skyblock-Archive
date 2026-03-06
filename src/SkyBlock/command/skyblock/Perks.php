<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\island\Island;
use SkyBlock\Main;
use SkyBlock\user\User;

class Perks extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'perks', "Shows all perks you get upon island level ups");
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (isset($args[1])) $this->sendMessage($sender, "§6Usage: /is perks");
        else $this->sendMessage($sender, "\n§ePerk  <==>  §dLevel\n§eIsland Bank Limit §f- §6Increases by 25000$ after §aevery §6level up.\n§eIsland Helper Limit §f- §6Increases by 1 after every §a5 §6level ups. §fMax - §6" . Island::HELPER_MAX . "\n§eIsland Homes Limit §f- §6Increases by 1 after every §a20 §6level ups. §fMax - §6" . Island::HOME_MAX . "\n§eIsland Worker Limit §f- §6Increases by 1 after every §a5 §6level ups. §fMax - §6" . Island::ROLE_MAX . "\n§eIsland CoOwner Limit §f- §6Increases by 1 after every §a30 §6level ups. §fMax - §6" . Island::COOWNER_MAX);
    }

}