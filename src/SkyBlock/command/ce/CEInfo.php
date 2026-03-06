<?php


namespace SkyBlock\command\ce;


use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Bow;
use pocketmine\item\TieredTool;
use pocketmine\item\ToolTier;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\enchants\BaseEnchantment;
use SkyBlock\Main;
use SkyBlock\util\Constants;

class CEInfo extends BaseCommand {
    /**
     * CEInfo constructor.
     *
     * @param Main $plugin
     */
    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'ceinfo', 'Get Info about a CE', '<ce name>');
    }

    /**
     * @param CommandSender $sender
     * @param string        $commandLabel
     * @param array         $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendUsage($sender, $commandLabel);
            return;
        }
        if (!isset($args[0])) {
            $hand = $sender->getInventory()->getItemInHand();
            if ($hand->getTypeId() == 0) {
                $this->sendMessage($sender, "§4[Error] §cYou are holding nothing!");
                return;
            }
            if (!$hand instanceof Armor && !$hand instanceof TieredTool && !$hand instanceof Bow) {
                $this->sendMessage($sender, "§4[Error] §cOnly tools and armors can be used!");
                return;
            }
            if ($hand instanceof TieredTool and $hand->getTier() < ToolTier::IRON()) {
                $this->sendMessage($sender, "§4[Error] §cOnly Iron, Diamond or Netherite tools can be used!");
                return;
            }
            if ($hand instanceof Armor && $hand->getMaxDurability() < Constants::ARMOR_TIER_CHAIN_MAX_DURABILITY) {
                $this->sendMessage($sender, "§4[Error] §cOnly Chain/Iron/Diamond armors can be used!");
                return;
            }
            if ($hand->getCount() !== 1) {
                $this->sendMessage($sender, "§4[Error] §cYou are holding more than one item!");
                return;
            }
            if ($this->func->countEnchants($hand, "ce") <= 0) {
                $this->sendMessage($sender, "§4[Error] §cThe item you're holding doesn't have any custom enchantments on it for info! Use /ceinfo <ce name> instead");
                return;
            }
            $enchantments = $this->pl->getEnchantments();
            $str = "§eDetecting CEs on your held item...\n";
            foreach ($hand->getEnchantments() as $e) {
                $id = BaseEnchantment::getEnchantmentId($e);
                if ($id >= 100) {
                    if (isset($enchantments[$id])) {
                        $name = $enchantments[$id][0];
                        $rarity = $enchantments[$id][3];
                        $type = $enchantments[$id][4];
                        $info = $enchantments[$id][5];
                        $str .= "§a$name §e=> §7$info! §eCategory => §a$rarity §r§eType => §f$type" . "\n";
                    }
                }
            }
            $sender->sendMessage($str . "Tip -> Chance of getting activated, and effect's level and duration depends on CE levels! Use /levelup to increase level of CE");
        } else {
            if (isset($args[1])) {
                $sender->sendMessage("§cUsage> /ceinfo or /ceinfo <ce name>");
                return;
            }
            $cename = strtolower($args[0]);
            if (is_int((int) $this->pl->getEnchantFactory()->getIdByEnchantName($cename))) {
                $this->formfunc->sendCEEnchantInfo($sender, $cename);
            } else {
                $this->sendMessage($sender, "§cCE not found! Use /celist to see all the CEs");
            }
        }
    }
}