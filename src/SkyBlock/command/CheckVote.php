<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\Main;

class CheckVote extends BaseCommand {
    /**
     * CheckVote constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'checkvote');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if ($sender instanceof Player) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        Main::getInstance()->checkVotes();
    }
}