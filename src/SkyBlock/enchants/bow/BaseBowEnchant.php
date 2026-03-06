<?php


namespace SkyBlock\enchants\bow;


use pocketmine\player\Player;
use SkyBlock\enchants\BaseEnchantment;

class BaseBowEnchant extends BaseEnchantment {
    public function isApplicableTo(Player $holder) : bool {
        return mt_rand(1, 5) === 1;
    }
}