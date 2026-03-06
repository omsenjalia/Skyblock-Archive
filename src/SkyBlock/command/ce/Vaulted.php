<?php


namespace SkyBlock\command\ce;


use pocketmine\command\CommandSender;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;

class Vaulted extends BaseCommand {
    /**
     * Vaulted constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'vaulted', 'Check Vaulted CEs');
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (isset($args[0])) {
            if (!$this->pl->isTrusted($sender->getName())) {
                $this->sendMessage($sender, "§6Usage: /vaulted");
            } else {
                if (!is_int((int) $args[0])) {
                    $this->sendMessage($sender, "§6Usage: /vaulted <ce id>");
                    return;
                }
                $vaultce = (int) $args[0];
                $this->pl->vaulted[] = $vaultce;
            }
            return;
        }
        $this->sendMessage($sender, "VAULTED CEs LIST:");
        $enchantments = $this->pl->getEnchantments();
        $text = "\n- ";
        foreach ($this->pl->getVaulted() as $id) {
            $text .= '§c' . $enchantments[$id][0] . '§f, ';
        }
        $text = substr($text, 0, -2);
        $sender->sendMessage($text . "\n§eGet VAULTED CEs from GODLY Relics, VAULTED CEs cant be obtained from Books or Envoys! Their production is stopped but they still work");
    }
}