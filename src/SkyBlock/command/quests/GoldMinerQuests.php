<?php

namespace SkyBlock\command\quests;

use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\tasks\DelayedMessageTask;

class GoldMinerQuests extends BaseCommand {

    public const PREFIX = "§l§e[§aKevin The Gold Miner§e]§r ";
    public static array $interacting = [];

    public static array $goldOreBroken = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'goldminerquests');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        $player = $sender;
        if (!$sender instanceof Player) {
            $this->sendUsage($sender, $commandLabel);
            return;
        }
        assert($player instanceof Player);
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
        if ($user === null) {
            return;
        }

        if (isset(self::$interacting[$player->getName()])) {
            return;
        }

        switch ($user->getQuests()->goldMiner) {
            case 0:
                self::$interacting[] = $player->getName();
                new DelayedMessageTask(self::PREFIX . "Hello!", $player, 1);
                new DelayedMessageTask(self::PREFIX . "My name is Kevin and I'm the gold master!", $player);
                new DelayedMessageTask(self::PREFIX . "I like a sorts of gold!", $player);
                new DelayedMessageTask(self::PREFIX . "Would ya mind bringing me 64 blocks of gold?", $player);
                $user->getQuests()->goldMiner = 1;
                unset(self::$interacting[$player->getName()]);
                break;
            case 1:
                self::$interacting[] = $player->getName();
                if (!$player->getInventory()->contains(VanillaBlocks::GOLD()->asItem()->setCount(64))) {
                    new DelayedMessageTask(self::PREFIX . "You don't seem to have the 64 gold blocks I asked ya for!", $player, 1);
                    unset(self::$interacting[$player->getName()]);
                    break;
                }
                $player->getInventory()->remove(VanillaBlocks::GOLD()->asItem()->setCount(64));
                new DelayedMessageTask(self::PREFIX . "Wow! This is a dream come true. Take 500 mana!", $player, 1);
                $user->addMana(500);
                new DelayedMessageTask(self::PREFIX . "Next please bring me a gold pickaxe. Mining is a hobby of mine.", $player);
                $user->getQuests()->goldMiner = 2;
                unset(self::$interacting[$player->getName()]);
                break;
            case 2:
                self::$interacting[] = $player->getName();
                if (!$player->getInventory()->contains(VanillaItems::GOLDEN_PICKAXE())) {
                    new DelayedMessageTask(self::PREFIX . "I don't think you have the gold pickaxe I wanted!", $player, 1);
                    unset(self::$interacting[$player->getName()]);
                    break;
                }
                $player->getInventory()->remove(VanillaItems::GOLDEN_PICKAXE());
                new DelayedMessageTask(self::PREFIX . "Thank you so much. I can't wait to get mining.", $player, 1);
                new DelayedMessageTask(self::PREFIX . "Speaking of mining, how about you go mine 1000 gold ore! Make sure to complete this before restart!!", $player);
                $user->getQuests()->goldMiner = 3;
                unset(self::$interacting[$player->getName()]);
                break;
            case 3:
                self::$interacting[] = $player->getName();
                if (!isset(self::$goldOreBroken[$player->getName()]) || self::$goldOreBroken[$player->getName()] < 1000) {
                    new DelayedMessageTask(self::PREFIX . "I said 1000 gold ore!! You've only mined " . self::$goldOreBroken[$player->getName()] . " gold ore!", $player, 1);
                    unset(self::$interacting[$player->getName()]);
                    break;
                }
                new DelayedMessageTask(self::PREFIX . "Don't you think that was fun. Take 2500 mana!", $player, 1);
                $user->addMana(2500);
                new DelayedMessageTask(self::PREFIX . "Now how about you go mine 10,000 more gold ore and report back to me!", $player);
                unset(self::$interacting[$player->getName()]);
                $user->getQuests()->goldMiner = 4;
                break;
            case 4:
                self::$interacting[] = $player->getName();
                if (!isset(self::$goldOreBroken[$player->getName()]) || self::$goldOreBroken[$player->getName()] < 1000) {
                    new DelayedMessageTask(self::PREFIX . "I said 10,000 gold ore!! You've only mined " . self::$goldOreBroken[$player->getName()] . " gold ore!", $player, 1);
                    unset(self::$interacting[$player->getName()]);
                    break;
                }
                $cebook = Main::getInstance()->getCEBook("legendary");
                if (!$player->getInventory()->canAddItem($cebook)) {
                    new DelayedMessageTask(self::PREFIX . "Don't you think that was fun? I would give you a legendary book but your inventory is full!", $player, 1);
                    unset(self::$interacting[$player->getName()]);
                    break;
                }
                new DelayedMessageTask(self::PREFIX . "Don't you think that was fun. Take a legendary book!", $player, 1);
                $player->getInventory()->addItem($cebook);
                new DelayedMessageTask(self::PREFIX . "TBD", $player);
                unset(self::$interacting[$player->getName()]);
                $user->getQuests()->goldMiner = 5;
                break;
            default:
                break;
        }

    }
}