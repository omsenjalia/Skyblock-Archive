<?php

namespace SkyBlock;

use pocketmine\player\Player;
use pocketmine\Server;

class ItemCloud {

    /** @var array */
    private array $items;

    /** @var bool */
    private bool $lock = false;

    /** @var string */
    private string $player;

    /**
     * ItemCloud constructor.
     *
     * @param string $username
     * @param array  $items
     */
    public function __construct(string $username, array $items) {
        $this->player = strtolower($username);
        $this->items = $items;
    }

    /**
     * @param string $namespace
     * @param int    $count
     * @param bool   $removeInv
     *
     * @return bool
     */
    public function addItem(string $namespace, int $count, bool $removeInv = true) : bool {
        if ($removeInv) {
            $p = Server::getInstance()->getPlayerExact($this->player);
            if (!$p instanceof Player) {
                return false;
            }
            $tmp = $count;
            foreach ($p->getInventory()->getContents() as $slot => $content) {
                if ($content->getVanillaName() === $namespace) {
                    if (!$content->hasEnchantments() and !$content->hasCustomName() and count($content->getLore()) < 2) {
                        if ($tmp <= 0) break;
                        $take = min($content->getCount(), $tmp);
                        $tmp -= $take;
                        $content->setCount($content->getCount() - $take);
                        $p->getInventory()->setItem($slot, $content);
                    }
                }
            }
        }

        if (isset($this->items[$namespace])) {
            $this->items[$namespace] += $count;
        } else {
            $this->items[$namespace] = $count;
        }
        return true;
    }

    /**
     * @param bool $lock
     */
    public function setLock(bool $lock) : void {
        $this->lock = $lock;
    }

    /**
     * @return bool
     */
    public function isLock() : bool {
        return $this->lock;
    }

    /**
     * @param string $namespace
     * @param int    $amount
     *
     * @return bool
     */
    public function itemExists(string $namespace, int $amount) : bool {
        $cnt = 0;
        foreach ($this->items as $i => $a) {
            if ($i === $namespace) {
                $cnt += $a;
                if ($amount <= $cnt) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string $namespace
     * @param int    $amount
     *
     * @return bool
     */
    public function removeItem(string $namespace, int $amount = 64) : bool {
        $cnt = 0;
        foreach ($this->items as $s => $i) {
            if ($s === $namespace) {
                $cnt += $i;
            }
        }
        if ((int) $cnt < $amount) {
            return false;
        }
        $this->items[$namespace] -= $amount;
        if ($this->items[$namespace] <= 0) {
            unset($this->items[$namespace]);
        }
        return true;
    }

    /**
     * @param string $namespace
     *
     * @return int
     */
    public function getCount(string $namespace) : int {
        return $this->items[$namespace] ?? 0;
    }

    /**
     * @return array
     */
    public function getAll() : array {
        return [
            $this->items,
            $this->player
        ];
    }

    /**
     * @return string
     */
    public function getPlayer() : string {
        return $this->player;
    }

    /**
     * @return array
     */
    public function getItems() : array {
        return $this->items;
    }

}