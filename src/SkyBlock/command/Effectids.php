<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Effectids extends BaseCommand {
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'effectids');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender->hasPermission("core.effect")) {
            $this->sendMessage($sender, TextFormat::YELLOW . "These effectids are for /effect.  Buy /effect on " . TextFormat::AQUA . "shop.fallentech.io");
        }

        $pages = [
            1 => "§b===== §6Effect Ids for /effect [1/3 Page]§b=====\n§f- §aSpeed §eId: 1\n§f- §aSlowness §eId: 2\n§f- §aHaste §eId: 3\n§f- §aMining Fatigue §eId: 4\n§f- §aStrength §eId: 5",
            2 => "§b===== §6Effect Ids for /effect [2/3 Page]§b=====\n§f- §aNausea §eId: 9\n§f- §aRegeneration §eId: 10\n§f- §aFire Resistance §eId: 12\n§f- §aWater Breathing §eId: 13\n§f- §aInvisibility §eId: 14",
            3 => "§b===== §6Effect Ids for /effect [3/3 Page]§b=====\n§f- §aBlindness §eId: 15\n§f- §aNight Vision §eId: 16\n§f- §aHunger §eId: 17\n§f- §aWeakness §eId: 18\n§f- §aPoison §eId: 19\n§f- §aWither §eId: 20\n§f- §aHealth Boost §eId: 21\n§f- §aAbsorption §eId: 22"
        ];

        $page = isset($args[0]) ? (int) $args[0] : 1;
        $sender->sendMessage($pages[$page] ?? $pages[1]);
    }
}