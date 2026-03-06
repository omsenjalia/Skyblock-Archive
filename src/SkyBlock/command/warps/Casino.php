<?php


namespace SkyBlock\command\warps;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

/**
 * @deprecated
 * Explore the NPCs instead
 * */
class Casino extends BaseCommand {
    /**
     * Casino constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'casino', 'Old warp command', '', true, ['crates', 'dropparty', 'dp', 'warzone', 'wz']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        Main::getInstance()->getFormFunctions()->sendWarpsConfirm($sender, "casino");
    }
}