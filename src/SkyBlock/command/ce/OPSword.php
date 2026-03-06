<?php


namespace SkyBlock\command\ce;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class OPSword extends BaseCommand {

    /**
     * OPSword constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'opsword', 'Give OP Sword to a player', "", true, [], "core.opsword");
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
        if (!isset($args[0]) or !isset($args[1])) {
            $sender->sendMessage("Usage: /opsword <level> <player>");
            return;
        }
        $level = $args[0];
        if (!is_int((int) $level) || $level < 1 || $level > 6) {
            $sender->sendMessage("Usage: /opsword <level = 1 - 6> <player>");
            return;
        }
        $level = (int) $level;
        $player = strtolower($args[1]);
        if (($user = $this->um->getOnlineUser($player)) === null) {
            $sender->sendMessage("§6- §cPlayer not online.");
            return;
        }
        $user->getPlayer()->getInventory()->addItem($this->func->opSword($level));
        $this->sendMessage($user->getPlayer(), "§eYou successfully claimed a §bLevel §6$level §aCustom Enchanted Diamond OP §eSword!");
    }
}