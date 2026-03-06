<?php


namespace SkyBlock;


use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class Tag {
    /** @var Main */
    public Main $pl;
    /** @var Config */
    public Config $config;
    /** @var array */
    private array $tags = [];

    public function __construct(Main $plugin) {
        $this->pl = $plugin;
        $this->setTags();
    }

    public function getTagString(int $id) {
        return $this->tags[$id];
    }

    public function getTags() : array {
        return $this->tags;
    }

    public function setTags() : void {
        $this->config = new Config($this->pl->getDataFolder() . "tags.json", Config::JSON);
        $this->tags = $this->config->get('tags', []);
    }

    public function getRandomTagString() {
        return $this->tags[mt_rand(0, (count($this->tags) - 1))];
    }

    public function getRandomTag() : Item {
        $item = VanillaItems::NAME_TAG();
        $item->setCustomName(TF::RESET . TF::BOLD . " {$this->getRandomTagString()} §r§bTag \n §eTap to redeem the Tag! ");
        return $item;
    }

    public function getTagId(string $tag) : int {
        $key = -1;
        foreach ($this->tags as $id => $name) {
            if (strtolower(TF::clean($tag)) == strtolower(TF::clean($name))) $key = $id;
        }
        return (int) ($key);
    }

}