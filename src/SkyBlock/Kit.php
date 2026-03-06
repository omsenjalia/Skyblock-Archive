<?php

namespace SkyBlock;

use pocketmine\block\tile\Container;
use pocketmine\block\VanillaBlocks;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\entity\{effect\EffectInstance, effect\StringToEffectParser};
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\lang\Language;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SkyBlock\util\Util;

class Kit {

    public Main $ak;
    public array $data;
    public string $name;
    public int $cost = 0;
    public int $coolDown;
    public array $pending = [];

    public function __construct(Main $ak, array $data, string $name, array $pending) {
        $this->ak = $ak;
        $this->data = $data;
        $this->name = $name;
        $this->coolDown = $this->getCoolDownSeconds();
        $this->pending = $pending;
    }

    public function getCoolDownSeconds() : int {
        $sec = 0;
        if (isset($this->data["cooldown"]["minutes"])) $sec += (int) $this->data["cooldown"]["minutes"];
        if (isset($this->data["cooldown"]["hours"])) $sec += (int) $this->data["cooldown"]["hours"] * 60;
        return (int) ($sec * 60);
    }

    public function getLowerCaseName() : string {
        return strtolower($this->name);
    }

    public function addTo(Player $player, $commands = true) {
        $inv = $player->getInventory();
        $chest = $this->getKitChest();
        if ($inv->canAddItem($chest)) $inv->addItem($chest);
        else {
            $player->sendMessage("§cYour inventory is full! §eEmpty 1 slot to claim chest kit.");
            return;
        }
        if ($commands) {
            if (isset($this->data["commands"]) and is_array($this->data["commands"])) {
                foreach ($this->data["commands"] as $cmd) {
                    $this->ak->getServer()->dispatchCommand(new ConsoleCommandSender($this->ak->getServer(), new Language("eng")), str_replace("{player}", '"' . $player->getName() . '"', $cmd));
                }
            }
        }
        if ($this->coolDown) {
            $this->pending[$player->getName()] = time() + $this->coolDown;
        }
        if ($commands) $player->sendMessage(TextFormat::YELLOW . "Selected Kit: " . TextFormat::GREEN . ucfirst($this->getName()));
    }

    public function getKitChest() : Item {
        $i = 0;
        $tag = [];
        foreach ($this->data["items"] as $itemString) {
            $item = $this->loadItem(...explode(":", $itemString));
            $tag[] = $item->nbtSerialize($i++);
        }
        $item = $this->loadItem(...explode(":", $this->data["helmet"]));
        $tag[] = $item->nbtSerialize($i++);
        $item = $this->loadItem(...explode(":", $this->data["chestplate"]));
        $tag[] = $item->nbtSerialize($i++);
        $item = $this->loadItem(...explode(":", $this->data["leggings"]));
        $tag[] = $item->nbtSerialize($i++);
        $item = $this->loadItem(...explode(":", $this->data["boots"]));
        $tag[] = $item->nbtSerialize($i++);

        $name = ucfirst($this->name);
        $effectball = VanillaItems::POPPED_CHORUS_FRUIT();
        $effectball->setCustomName("§l§a{$name} §r§o§fEffects \n Tap a block to claim effects");
        $tag[] = $effectball->nbtSerialize($i++);

        if (isset($this->data["xp"])) {
            $xpball = VanillaItems::NAUTILUS_SHELL();
            $xpball->setCustomName("§l§a{$name} §r§o§fXP \n Tap a block to claim XP");
            $tag[] = $xpball->nbtSerialize($i++);
        }

        if (isset($this->data["chips"])) {
            $chips = VanillaItems::HEART_OF_THE_SEA();
            $chips->setCustomName("§l§a{$name} §r§o§fChips \n Tap a block to claim Chips");
            $tag[] = $chips->nbtSerialize($i++);
        }

        if (isset($this->data["money"])) {
            $money = $this->ak->getCheque($this->data["money"]);
            $tag[] = $money->nbtSerialize($i++);
        }
        if (isset($this->data["key"])) {
            $keydata = explode(";", $this->data["key"]);
            foreach ($keydata as $keys) {
                $key = explode(":", $keys);
                $tag[] = $this->ak->getCrateKeys($key[0], $key[1])->nbtSerialize($i++);
            }
        }

        $ctag = new CompoundTag();
        $ctag->setTag(Container::TAG_ITEMS, new ListTag($tag, NBT::TAG_Compound));

        $chest = VanillaBlocks::CHEST()->asItem();
        $chest->setNamedTag($chest->getNamedTag());
        $chest->setCustomBlockData($ctag);

        $chest->setCustomName("§o§l§a{$name} §fKit\n§r§ePlace this chest\n§eand open it to get the items!");
        return $chest;
    }

    public function loadItem(int $id = 0, int $damage = 0, int $count = 1, string $name = "default", ...$enchantments) : Item {

        $item = LegacyStringToItemParser::getInstance()->parse($id . ":" . $damage);
        $item = $item->setCount($count);
        if (strtolower($name) !== "default") {
            $item->setCustomName($name);
        }
        foreach ($enchantments as $key => $name_level) {
            if ($key % 2 === 0) {
                $ench = StringToEnchantmentParser::getInstance()->parse($name_level);
            } else { //Level expected
                if (isset($ench)) {
                    $item->addEnchantment(new EnchantmentInstance($ench, $name_level));
                }
            }
        }
        return $item;
    }

    public function getName() : string {
        return $this->name;
    }

    public function loadEffect(string $name = "INVALID", int $seconds = 60, int $amplifier = 1) : ?EffectInstance {
        if (($e = StringToEffectParser::getInstance()->parse(strtolower($name))) !== null)
            return new EffectInstance($e, $seconds * 20, $amplifier, false);
        else return null;
    }

    public function getCoolDownLeft(Player $player) : ?string {
        $name = $player->getName();
        if (!isset($this->pending[$name])) return null;
        $left = $this->pending[$name] - time();
        if ($left <= 0) {
            unset($this->pending[$name]);
            return null;
        }
        return Util::getTimePlayed($left);
    }

}