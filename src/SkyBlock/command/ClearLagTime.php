<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class ClearLagTime extends BaseCommand {

    /**
     * ClearLagTime constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'clearlagtime', 'Clear entities task time left', '', true, ['clt', 'clearlaggtime']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        $difference = Main::getInstance()->clt;
        $minutes = (int) ($difference / 60);
        $seconds = $difference % 60;
        if ($seconds === 0) {
            $this->sendMessage($sender, TextFormat::YELLOW . "Time left before entities are cleared: " . TextFormat::RED . $minutes . TextFormat::YELLOW . " minutes!");
        } else {
            $this->sendMessage($sender, TextFormat::YELLOW . "Time left before entities are cleared: " . TextFormat::RED . $minutes . TextFormat::YELLOW . " minutes and " . TextFormat::RED . $seconds . " seconds!");
        }
    }

}