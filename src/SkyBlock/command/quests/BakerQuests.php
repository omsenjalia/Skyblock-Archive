<?php

namespace SkyBlock\command\quests;

use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use SkyBlock\command\BaseCommand;
use SkyBlock\Main;
use SkyBlock\util\Util;
use SOFe\AwaitGenerator\Await;

class BakerQuests extends BaseCommand {

    public const PREFIX = "§l§e[§aAntony The Baker§e]§r ";
    public static array $interacting = [];

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'bakerquests');
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

        switch ($user->getQuests()->baker) {
            case 0:
                Await::f2c(function() use ($player, $user) : \Generator {
                    self::$interacting[] = $player->getName();
                    $player->sendMessage(self::PREFIX . "Why, hello there " . $player->getName() . "!");
                    yield from Util::sleep(Main::getInstance(), 20);
                    $player->sendMessage(self::PREFIX . "I'm Antony, the Baker!");
                    yield from Util::sleep(Main::getInstance(), 20);
                    $player->sendMessage(self::PREFIX . "I bake cakes, bread, the whole lot.");
                    yield from Util::sleep(Main::getInstance(), 20);
                    $player->sendMessage(self::PREFIX . "Say, if you bring me some items i'll trade with you!");
                    $user->getQuests()->baker = 1;
                    unset(self::$interacting[$player->getName()]);
                }
                );
                break;
            case 1:
                self::$interacting[] = $player->getName();
                $player->sendMessage(self::PREFIX . "Please bring me 10 sugarcane!");
                $user->getQuests()->baker = 2;
                unset(self::$interacting[$player->getName()]);
                break;
            case 2:
                Await::f2c(function() use ($player, $user) : \Generator {
                    self::$interacting[] = $player->getName();
                    if (!$player->getInventory()->contains(VanillaBlocks::SUGARCANE()->asItem()->setCount(10))) {
                        $player->sendMessage(self::PREFIX . "Remember, I asked for 10 sugarcane!");
                        unset(self::$interacting[$player->getName()]);
                        return;
                    }
                    $player->getInventory()->remove(VanillaBlocks::SUGARCANE()->asItem()->setCount(10));
                    $player->sendMessage(self::PREFIX . "That was fast! That's one ingredient down.");
                    yield from Util::sleep(Main::getInstance(), 20);
                    $player->sendMessage(self::PREFIX . "Next get me 50 wheat. This is essential!");
                    $user->getQuests()->baker = 3;
                    unset(self::$interacting[$player->getName()]);
                }
                );
                break;
            case 3:
                Await::f2c(function() use ($player, $user) : \Generator {
                    self::$interacting[] = $player->getName();
                    if (!$player->getInventory()->contains(VanillaItems::WHEAT()->setCount(50))) {
                        $player->sendMessage(self::PREFIX . "You dont seem to have the 50 wheat I asked for!");
                        unset(self::$interacting[$player->getName()]);
                        return;
                    }
                    $player->getInventory()->remove(VanillaItems::WHEAT()->setCount(50));
                    $player->sendMessage(self::PREFIX . "Another ingredient down!");
                    yield from Util::sleep(Main::getInstance(), 20);
                    $player->sendMessage(self::PREFIX . "For the final ingredient, bring me 25 diamonds!");
                    $user->getQuests()->baker = 4;
                    unset(self::$interacting[$player->getName()]);
                }
                );
                break;
            case 4:
                Await::f2c(function() use ($player, $user) : \Generator {
                    self::$interacting[] = $player->getName();
                    if (!$player->getInventory()->contains(VanillaItems::DIAMOND()->setCount(25))) {
                        $player->sendMessage(self::PREFIX . "I don't think you have the 25 diamonds!");
                        unset(self::$interacting[$player->getName()]);
                        return;
                    }
                    $player->getInventory()->remove(VanillaItems::WHEAT()->setCount(25));
                    $player->sendMessage(self::PREFIX . "And there we go!");
                    $mysticKey = Main::getInstance()->getCrateKeys("mystic")->setCount(5);
                    yield from Util::sleep(Main::getInstance(), 20);
                    if (!$player->getInventory()->canAddItem($mysticKey)) {
                        $player->sendMessage(self::PREFIX . "I baked up something for you but your inventory is full.");
                        unset(self::$interacting[$player->getName()]);
                        return;
                    }
                    $player->sendMessage(self::PREFIX . "I baked up these for you. Enjoy!");
                    $player->getInventory()->addItem($mysticKey);
                    $user->getQuests()->baker = 5;
                    unset(self::$interacting[$player->getName()]);
                }
                );
                break;
            case 5:
                self::$interacting[] = $player->getName();

                $user->getQuests()->baker = 6;
                unset(self::$interacting[$player->getName()]);
                break;
            case 6:
                self::$interacting[] = $player->getName();

                $user->getQuests()->baker = 5;
                unset(self::$interacting[$player->getName()]);
                break;

        }
    }

}