<?php


namespace SkyBlock\util;


use DateTime;
use Exception;
use pocketmine\block\Block;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\inventory\BaseInventory;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\StringToItemParser;
use pocketmine\math\Facing;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat as TF;
use SkyBlock\enchants\BaseEnchantment;
use SOFe\AwaitGenerator\Await;

final class Util {

    /**
     * @param int $id
     *
     * @return string
     */
    public static function getOS(int $id) : string {
        //var_dump($id);
        return match ($id) {
            1 => "Android",
            2 => "IOS",
            3 => "OSX",
            4 => "Amazon",
            5 => "GearVR",
            6 => "HoloLens",
            7 => "Win10",
            8 => "Win32",
            9 => "Dedicated",
            10 => "TVOS",
            11 => "PS4",
            12 => "Nintendo",
            13 => "XBOX",
            14 => "WinPhone",
            default => "N/A",
        };
    }

    /**
     * @param Block $block
     *
     * @return Block
     */
    public static function getFrontBlock(Block $block) : Block {
        return $block->getSide($block->getFacing());
    }

    /**
     * @param Block $block
     *
     * @return Block
     */
    public static function getRearBlock(Block $block) : Block {
        return $block->getSide(Facing::opposite($block->getFacing()));
    }

    public static function getItemFromArg(string $item) : ?Item { // removed old id support
        return StringToItemParser::getInstance()->parse($item) ?? LegacyStringToItemParser::getInstance()->parse($item);
    }

    /**
     * @param BaseInventory $inv
     * @param Item          $item
     *
     * @return int
     */
    public static function getSlotsForItem(BaseInventory $inv, Item $item) : int {
        $count = 0;
        for ($i = 0, $size = $inv->getSize(); $i < $size; ++$i) {
            $slot = $inv->getItem($i);
            if ($item->equals($slot)) {
                if (($diff = min($slot->getMaxStackSize(), $item->getMaxStackSize()) - $slot->getCount()) > 0) {
                    $count += $diff;
                }
            } else if ($slot->isNull()) {
                $count += min($inv->getMaxStackSize(), $item->getMaxStackSize());
            }
        }
        return $count;
    }

    /**
     * @param int  $seconds
     * @param bool $spaced
     *
     * @return string
     */
    public static function getTimePlayed(int $seconds, bool $spaced = true) : string {
        try {
            $dtF = new DateTime('@0');
            $dtT = new DateTime("@$seconds");
            $format = $spaced ? "%ady %hhr %imn" : "%ady%hhr%imn";
            return $dtF->diff($dtT)->format($format);
        } catch (Exception) {
            return "null";
        }
    }

    /**
     * @param array $lores
     *
     * @return string
     */
    public static function getLoreString(array $lores) : string {
        $str = "";
        if (count($lores) < 2)
            return $str;
        $str .= "\n§2➼ §eExtra: ";
        foreach ($lores as $lore) {
            if ($lore !== "")
                $str .= $lore . ", ";
        }
        return substr($str, 0, -2);
    }

    /**
     * @param Item   $item
     * @param string $default
     * @param array  $exlude
     * @param string $prefix
     *
     * @return string
     */
    public static function getNameOfItem(Item $item, string $default = "", array $exlude = [], string $prefix = " ") : string {
        $custom = preg_split('/\r\n|\r|\n/', $item->getCustomName());
        if (in_array($item->getTypeId(), $exlude, true))
            return "";
        if ($item->hasCustomName())
            $custom1 = (TF::clean($custom[0]) === $item->getVanillaName()) ? $default : $default . $prefix . "§a`" . $custom[0] . "§a`"; else $custom1 = $default;
        return $custom1;
    }

    public static function insertArrayEvery(array $array, int $every, string $element = "") : array {
        $index = 0;
        foreach ($array as $value) {
            if (($index + 1) % $every == 0) {
                array_splice($array, $index, 0, $element);
            }
            $index++;
        }
        return $array;
    }

    public static function getLooting(Item $item) : int {
        return BaseEnchantment::getEnchantmentLevel($item, EnchantmentIds::LOOTING);
    }

    public static function sleep(Plugin $plugin, int $ticks) : \Generator {
        yield from Await::promise(function($resolve) use ($plugin, $ticks) : void {
            $task = new ClosureTask(fn() => $resolve());
            $plugin->getScheduler()->scheduleDelayedTask($task, $ticks);
        }
        );
    }

    public static function convertToFloat($value) : float {
        if (is_numeric($value)) {
            return (float) $value;
        } elseif (is_int($value) || is_float($value) || is_string($value)) {
            return (float) $value;
        } else {
            return 0.0;
        }
    }
}