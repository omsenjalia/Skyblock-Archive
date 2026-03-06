<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use SkyBlock\Main;

class Enchantids extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'enchantids');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        $pages = [
            1 => "§b===== §6Enchantment Ids for /enchant [1/2 Page]§b=====\n§f- §aProtection §eId: 0\n§f- §aFire Protection §eId: 1\n§f- §aFeather Falling §eId: 2\n§f- §aBlast Protection §eId: 3\n§f- §aProjectile Protection §eId: 5\n§f- §aRespiration §eId: 6\n§f- §aDepth Strider §eId: 7\n§f- §aAqua Affinity §eId: 8\n§f- §aSharpness §eId: 9",
            2 => "§b===== §6Enchantment Ids for /enchant [2/2 Page]§b=====\n§f- §aSmite §eId: 10\n§f- §aBane of Arthropods §eId: 11\n§f- §aKnockback §eId: 12\n§f- §aFire Aspect §eId: 13\n§f- §aLooting §eId: 14\n§f- §aEfficiency §eId: 15\n§f- §aSilk Touch §eId: 16\n§f- §aUnbreaking §eId: 17\n§f- §aFortune §eId: 18\n§f- §aBow Power §eId: 19\n§f- §aBow Punch §eId: 20\n§f- §aBow Flame §eId: 21\n§f- §aBow Infinity §eId: 22"
        ];

        $page = isset($args[0]) ? (int) $args[0] : 1;
        $sender->sendMessage($pages[$page] ?? $pages[1]);
    }

}