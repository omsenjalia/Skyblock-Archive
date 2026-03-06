<?php

/**
 *    ____           _            ____  _____
 *   / ___|___  _ __| |_ _____  _|  _ \| ____|
 *  | |   / _ \| '__| __/ _ \ \/ / |_) |  _|
 *  | |__| (_) | |  | ||  __/>  <|  __/| |___
 *   \____\___/|_|   \__\___/_/\_\_|   |_____|
 * Copyright (C) CortexPE - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Ralph B <mcpe4life62@gmail.com>, December 11, 2017
 */

declare(strict_types=1);

namespace SkyBlock\tasks;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SkyBlock\Arena;

class PreGameTimerTask extends Task {
    private Arena $arena;
    private int $time = 30;

    /**
     * @param Arena $arena
     */
    public function __construct(Arena $arena) {
        $this->arena = $arena;
    }

    public function onRun() : void {
        $msg = TextFormat::BOLD . TextFormat::RED . "(!) " . TextFormat::RESET . TextFormat::GREEN . " King of The Hill event is starting in " . TextFormat::GOLD . $this->time . TextFormat::GREEN . " seconds. Join the event by using /koth join!";
        if (in_array($this->time, [30, 20, 15, 3, 2, 1])) {
            Server::getInstance()->broadcastMessage($msg);
        }
        $this->time--;
        if ($this->time < 1) {
            $this->arena->startGame();
            Server::getInstance()->broadcastMessage(TextFormat::BOLD . TextFormat::RESET . "(!) " . TextFormat::RESET . TextFormat::GREEN . " King of The Hill event has started. Join the event by using /koth join!");
            $this->getHandler()->cancel();
            return;
        }
        $this->arena->sendTip(TextFormat::BOLD . "(!) " . TextFormat::RESET . TextFormat::GREEN . " King of The Hill event is starting in " . TextFormat::GOLD . $this->time . TextFormat::GREEN . " seconds!");
    }

}