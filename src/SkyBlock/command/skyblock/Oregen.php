<?php

namespace SkyBlock\command\skyblock;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\Values;


class Oregen extends BaseCommand {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'oregen', 'Open oregen menu for island.');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendUsage($sender, $commandLabel);
            return;
        }
        if (in_array($sender->getPosition()->getWorld()->getDisplayName(), Values::PVP_WORLDS, true)) {
            $this->sendMessage($sender, "§4[Error] §cYou can't use this command here!");
            return;
        }
        $this->formfunc->getShop()->sendOregenUpgrade($sender);
    }
}