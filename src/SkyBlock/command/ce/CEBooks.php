<?php


namespace SkyBlock\command\ce;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class CEBooks extends BaseCommand {
    /**
     * CEBooks constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'cebooks', 'Give CE Books to player as reward', '[player]', true, ['cebook'], "core.cebooks");
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!($this->pl->hasOp($sender))) {
            $sender->sendMessage("Insufficient permissions");
            return;
        }
        if ($sender instanceof Player && !$this->pl->isTrusted($sender->getName())) {
            $this->sendMessage($sender, "§4[Error]§c No permission");
            return;
        }
        if (isset($args[0]) and !isset($args[1])) {
            $player = strtolower($args[0]);
            if (($user = $this->um->getOnlineUser($player)) === null) {
                $sender->sendMessage("§4[Error] §cPlayer not online!");
                return;
            }
            $user->getPlayer()->getInventory()->addItem($this->pl->getCEBook($this->func->getBook()));
            return;
        }
        if (isset($args[1])) {
            $count = 1;
            if (isset($args[2])) {
                if (is_int((int) $args[2])) {
                    $count = (int) $args[2];
                } else {
                    $sender->sendMessage("Usage: /cebook <player> <book> <count>");
                    return;
                }
            }
            $player = strtolower($args[0]);
            if (($user = $this->um->getOnlineUser($player)) === null) {
                $sender->sendMessage("§4[Error] §cPlayer not online!");
                return;
            }
            $this->sendMessage($sender, "§eSuccessfully gave §7x§c$count §b§l" . ucfirst($args[1]) . " §r§eCE Book to §a{$args[0]}");
            $this->sendMessage($user->getPlayer(), "§eSuccessfully received §7x§c$count §b§l" . ucfirst($args[1]) . " §r§eCE Book");
            $user->getPlayer()->getInventory()->addItem($this->pl->getCEBook($args[1], $count));
        }
    }
}