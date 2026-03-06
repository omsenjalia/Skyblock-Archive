<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class ClearEffects extends BaseCommand {
    /**
     * ClearEffects constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'cleareffects', 'Clear effects on you');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        $sender->getEffects()->clear();
        $this->sendMessage($sender, TextFormat::GREEN . "All your effects have been cleared!");
    }
}