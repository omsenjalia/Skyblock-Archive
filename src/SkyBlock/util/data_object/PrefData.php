<?php


namespace SkyBlock\util\data_object;


use SkyBlock\util\Constants;

final class PrefData extends JsonObject {

    /** @var int */
    public int $ce_act_type = Constants::CE_TIP;

    /** @var bool */
    public bool $scoreboard_enabled = true;

    /** @var bool */
    public bool $capes_enabled = true;

    /** @var bool */
    public bool $welcome_msg = true;

    /** @var bool */
    public bool $chair_feature = false;

    /** @var bool */
    public bool $button_size = false;
    public bool $exclcmdmessages = true;

    /** @var bool */
    public bool $chest_ui = false;

    public bool $showIslandLevel = true;
    public bool $showIslandRank = true;
    public bool $showIslandName = true;
    public bool $showRanks = true;
    public bool $showOS = false;
    public bool $showTags = true;
    public bool $showGangs = true;

    public bool $saveLastPosition = true;


    public bool $useCompactChat = true;

}