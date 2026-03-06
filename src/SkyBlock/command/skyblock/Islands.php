<?php


namespace SkyBlock\command\skyblock;


use pocketmine\player\Player;
use SkyBlock\Main;
use SkyBlock\user\User;

class Islands extends BaseSkyblock {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'islands', 'See the islands you have or are helper on.');
    }

    public function execute(Player $sender, User $user, array $args) : void {
        if (!isset($args[1])) {
            $sender->sendMessage("==================== §eYOUR ISLANDS §f====================\n§bOwned Islands: §a{$user->getIsland()}\n§bHelper on Islands: §e{$user->getIslandsString()}\n§bHelper limit at your rank: §f{$this->func->getUserHelperLimit($sender)}\n§eBuy a premium rank at §ashop.fallentech.io §eto increase this limit!\n§f=====================================================");
        } else {
            $this->sendMessage($sender, "§cUsage: /is islands");
        }
    }

}