<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\Main;

class CheckEnch extends BaseCommand {
    /**
     * CheckEnch constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'checkench', 'Check enchantments on the held item');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        if (!$sender->getInventory()->getItemInHand()->hasEnchantments()) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " The item you are holding has no enchantments!");
            return;
        }
        $enchantments = $sender->getInventory()->getItemInHand()->getEnchantments();
        $this->sendMessage($sender, TextFormat::YELLOW . "Enchantments:");
        foreach ($enchantments as $enchantment) {
            $id = BaseEnchantment::getEnchantmentId($enchantment);
            $this->sendMessage($sender, TextFormat::GREEN . $enchantment->getType()->getName() . " - ID: " . $id);
        }
    }
}