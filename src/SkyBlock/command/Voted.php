<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\Main;

class Voted extends BaseCommand {
    /**
     * Voted constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'voted');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if ($sender instanceof Player) {
            $this->sendMessage($sender, self::NO_PERMISSION);
            return;
        }
        $votes = Main::getInstance()->getVote();
        Main::getInstance()->setVote($votes--);
        Main::getInstance()->checkVotes(false);
    }
}