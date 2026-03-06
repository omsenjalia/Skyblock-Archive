<?php


namespace SkyBlock\command\ce;


use pocketmine\command\CommandSender;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\Main;

class CEVote extends BaseCommand {
    /**
     * CEVote constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'cevote', 'CE vote Reward', '[player]', true, [], "core.cevote");
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$this->pl->hasOp($sender)) {
            $sender->sendMessage("§4[Error]§c No permission!");
            return;
        }
        if ($sender instanceof Player && !$this->pl->isTrusted($sender->getName())) {
            $this->sendMessage($sender, "§4[Error]§c No permission");
            return;
        }
        if (!isset($args[0])) {
            $sender->sendMessage("§cUsage: /ce <player>");
            return;
        }
        $player = strtolower($args[0]);
        if (($user = $this->um->getOnlineUser($player)) === null) {
            $sender->sendMessage("§4[Error] §cPlayer not online!");
            return;
        }
        $sword = [VanillaItems::IRON_SWORD(), VanillaItems::DIAMOND_SWORD()];
        $item = $sword[mt_rand(0, 1)];
        $enchantment = BaseEnchantment::getEnchantment($this->func->randomCEType());
        $enchantment2 = BaseEnchantment::getEnchantment($this->func->randomCEType());
        $item->addEnchantment(new EnchantmentInstance($enchantment, mt_rand(1, 5)));
        $item->addEnchantment(new EnchantmentInstance($enchantment2, mt_rand(1, 4)));
        $hand = $this->func->setEnchantmentNames($item, false);
        $user->getPlayer()->getInventory()->addItem($hand);
        $armor = [
            VanillaItems::DIAMOND_HELMET(), VanillaItems::CHAINMAIL_HELMET(), VanillaItems::IRON_HELMET(), VanillaItems::DIAMOND_CHESTPLATE(),
            VanillaItems::CHAINMAIL_CHESTPLATE(), VanillaItems::IRON_CHESTPLATE(), VanillaItems::DIAMOND_LEGGINGS(), VanillaItems::CHAINMAIL_LEGGINGS(), VanillaItems::IRON_LEGGINGS(),
            VanillaItems::DIAMOND_BOOTS(), VanillaItems::CHAINMAIL_BOOTS(), VanillaItems::IRON_BOOTS()
        ];
        $item = $armor[mt_rand(0, 11)];
        $enchantment = BaseEnchantment::getEnchantment($this->func->randomCEType('armor'));
        $item->addEnchantment(new EnchantmentInstance($enchantment, mt_rand(1, 6)));
        $hand = $this->func->setEnchantmentNames($item, false);
        $user->getPlayer()->getInventory()->addItem($hand);
        $this->sendMessage($user->getPlayer(), "§eYou successfully claimed a custom enchanted Sword and a custom enchanted Armor Piece! §aDo /ceinfo <ce> to see what each ce does!");
    }
}