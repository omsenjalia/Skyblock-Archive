<?php


namespace SkyBlock\command;


use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use SkyBlock\Main;

class Ranks extends BaseCommand {

    public function __construct(Main $plugin) {
        parent::__construct($plugin, 'ranks');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        $ranks = [
            "Guest"      => "Default Rank you get when joining server",
            "King"       => "Premium Rank with more features",
            "VIP"        => "Premium Rank with more features with all King's features",
            "Myth"       => "Premium Rank with more features with all VIP's features",
            "SkyLord"    => "Premium Rank with more features with all Myth's features",
            "SkyGOD"     => "Premium Rank with more features with all SkyLord's features",
            "SkyZEUS"    => "Premium Rank with more features with all SkyGOD's features",
            "SkyELITE"   => "Premium Rank with the best features with all SkyZEUS's features",
            "SkyHULK"    => "Premium Rank with the best features with all SkyELITE's features",
            "SkyWARRIOR" => "Premium Rank with the best features with all SkyHULK's features",
            "Trainee"    => "Staff Rank with /fly, /mute and /kick",
            "Builder"    => "Staff Rank with build commands",
            "YouTuber"   => "Partner Rank with /fly",
            "Streamer"   => "Partner Rank with /fly",
            "Helper"     => "Staff Rank with /mute, /fly, /ban, /vanish, /spectate, /freeze",
            "MOD"        => "Staff Rank with all Helper commands and /gmc, /god",
            "Admin"      => "Staff Rank with all MOD commands /ban-ip, /gmc, /tp",
            "Head-Admin" => "Staff Rank with OP",
            "CoOwner"    => "Staff Rank with OP",
            "Owner"      => "Max"
        ];
        $str = TextFormat::DARK_GREEN . "-----------" . TextFormat::BOLD . TextFormat::AQUA . " [" . TextFormat::GREEN . "Ranks Help" . TextFormat::AQUA . "] " . TextFormat::RESET . TextFormat::DARK_GREEN . "-----------\n";
        foreach ($ranks as $rank => $description)
            $str .= TextFormat::AQUA . "§e/helpme " . TextFormat::GREEN . "§e{$rank}: " . TextFormat::RESET . TextFormat::GRAY . $description . "\n";
        $this->sendMessage($sender, $str . TextFormat::DARK_GREEN . "-----------------" . TextFormat::BOLD . TextFormat::AQUA . " [" . TextFormat::GREEN . "1/1" . TextFormat::AQUA . "] " . TextFormat::RESET . TextFormat::DARK_GREEN . "-----------------");

    }
}