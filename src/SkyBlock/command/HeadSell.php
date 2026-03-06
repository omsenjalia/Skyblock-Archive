<?php


namespace SkyBlock\command;


use pocketmine\block\MobHead;
use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\Data;
use SkyBlock\Main;

/**
 * @deprecated
 * */
class HeadSell extends BaseCommand {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'headsell', 'Sell heads in your inventory');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $this->sendMessage($sender, self::NO_CONSOLE);
            return;
        }
        $amount = 0;
        $items = $sender->getInventory()->getContents();
        foreach ($items as $item) {
            $block = $item->getBlock();
            if ($block->getTypeId() === VanillaBlocks::MOB_HEAD()) {
                assert($block instanceof MobHead);
                if ($block->getMobHeadType() === MobHeadType::PLAYER) {
                    if ($item->hasCustomName()) {
                        if (count(explode("\n", $item->getCustomName())) > 0) {
                            $count = $item->getCount();
                            $amount += $count;
                            $sender->getInventory()->remove($item);
                        }
                    }
                }
            }
        }
        $money = $amount * Data::$commandHeadSellPerHead;
        if ($money === 0) {
            $this->sendMessage($sender, TextFormat::RED . "[ERROR]" . TextFormat::YELLOW . " You have no heads to sell!");
        } else {
            $user = Main::getInstance()->getUserManager()->getOnlineUser($sender->getName());
            $user->addMoney($money);
            $this->sendMessage($sender, TextFormat::YELLOW . "You sold $amount heads for $money. New Balance is $" . $user->getMoney());
        }
    }
}