<?php

namespace SkyBlock\command\quests;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class IslandQuest extends BaseCommand {

    public const LIONEL_PREFIX = "§l§e[§aLionel]§e]§r";

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'islandquest');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {

    }

}