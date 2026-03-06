<?php


namespace SkyBlock\command\ce;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\Values;

class CEShop extends BaseCommand {
    /**
     * CEShop constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'ceshop', 'CE Shop Menu');
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!$sender instanceof Player) {
            $this->sendUsage($sender, $commandLabel);
            return;
        }
        if ($sender->getWorld()->getDisplayName() === Values::PVP_WORLD) {
            $this->sendMessage($sender, "§4[Error] §cYou can't use this command here!");
            return;
        }
        $this->formfunc->getShop()->sendCEBooksMenu($sender);
    }
}