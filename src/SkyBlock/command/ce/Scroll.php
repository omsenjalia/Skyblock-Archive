<?php


namespace SkyBlock\command\ce;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class Scroll extends BaseCommand {
    /**
     * Scroll constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'scroll', 'Give a scroll', '[player] <scroll name>', true, [], "core.scroll");
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->pl->hasOp($sender)) {
            $sender->sendMessage("§c> No perms!");
            return;
        }
        if ($sender instanceof Player && !$this->pl->isTrusted($sender->getName())) {
            $this->sendMessage($sender, "§4[Error]§c No permission");
            return;
        }
        if (!isset($args[0]) or !isset($args[1])) {
            $this->sendMessage($sender, "/scroll <player> <" . implode(", ", array_keys($this->pl->scrolls)) . ">");
            return;
        }
        $playerName = strtolower($args[0]);
        if (($user = $this->um->getOnlineUser($playerName)) === null) {
            $this->sendMessage($sender, "§4[Error]§c That player is not online!");
            return;
        }
        $scrollname = strtolower($args[1]);
        if (!isset($this->pl->scrolls[$scrollname])) {
            $sender->sendMessage("§c> Scroll not found!");
            return;
        }
        $user->getPlayer()->getInventory()->addItem($this->pl->getScrolls($scrollname));
        $this->sendMessage($user->getPlayer(), "§eYou successfully claimed a §a`$scrollname` §escroll!");
    }
}