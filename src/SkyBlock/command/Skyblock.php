<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use SkyBlock\command\skyblock\SkyblockFactory;
use SkyBlock\Main;

class Skyblock extends BaseCommand {

    /**
     * Skyblock constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'is', 'Skyblock/Island Command', 'help');
        $this->pl->sf = new SkyblockFactory($plugin);
        $this->pl->sf->init();
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!isset($args[0])) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Usage: /is help");
        } else {
            Main::getInstance()->sf->execute($sender, $args);
        }
    }
}