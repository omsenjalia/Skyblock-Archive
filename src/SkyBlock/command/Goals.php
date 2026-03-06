<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;
use SkyBlock\util\Values;

class Goals extends BaseCommand {
    /**
     * Goals constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'goals', 'Goals Menu', '', true, ['quest', 'goal', 'challenges', 'challenge', 'ch', 'quests', 'missions']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if ($sender->getPosition()->getWorld()->getDisplayName() === Values::PVP_WORLD) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You cannot use that command here!");
            return;
        }
        $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " Goals have been disabled indefinitely and have been replaced with quests!");
    }
}