<?php


namespace SkyBlock\item;


use pocketmine\item\Book;
use pocketmine\item\ItemIdentifier;

class CustomBook extends Book {
    public function __construct(ItemIdentifier $identifier, string $name = "Unknown") {
        parent::__construct($identifier, $name);
    }
}