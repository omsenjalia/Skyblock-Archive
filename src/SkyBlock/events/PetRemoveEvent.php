<?php

declare(strict_types=1);

namespace SkyBlock\events;

class PetRemoveEvent extends PetEvent {
    /**
     * Returns the owner of the pet about to be spawned.
     * @return string
     */
    public function getPlayerName() : string {
        return $this->pet->getPetOwnerName();
    }
}