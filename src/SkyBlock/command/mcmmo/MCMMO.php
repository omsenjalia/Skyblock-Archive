<?php


namespace SkyBlock\command\mcmmo;


use pocketmine\command\CommandSender;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class MCMMO extends BaseCommand {
    /**
     * MCMMO constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'mcmmo', 'MCMMO Help', '', true, ['mmo']);
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        $this->sendMessage($sender, "§eMCMMO §eby §a@§bInfernus101§e! Use /mcstats or /mctop <type>");
    }
}