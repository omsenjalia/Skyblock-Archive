<?php

namespace SkyBlock\item\items;


class NetheriteScrap extends \pocketmine\item\Item {
    public function __construct(\pocketmine\item\ItemIdentifier $identifier, string $name = "Unknown") {
        parent::__construct($identifier, $name);
        $this->setLore(["Scrap Moment"]);
    }
}