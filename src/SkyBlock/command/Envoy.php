<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Envoy extends BaseCommand {
    /**
     * Envoy constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'envoy', 'Check envoy spawn time', '', true, ['envoys']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        $minutes = Main::getInstance()->envoyTimer;
        if ($minutes > 0) {
            $this->sendMessage($sender, TextFormat::AQUA . TextFormat::BOLD . "> Envoys will spawn in " . TextFormat::RED . $minutes . TextFormat::AQUA . " minutes!");
        } else {
            $this->sendMessage($sender, TextFormat::AQUA . TextFormat::BOLD . " Envoys have already spawned. You will need to wait until next restart now!");
        }
    }

}