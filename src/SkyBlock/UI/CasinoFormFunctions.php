<?php


namespace SkyBlock\UI;


use jojoe77777\FormAPI\CustomForm;
use pocketmine\network\mcpe\protocol\types\inventory\MismatchTransactionData;
use pocketmine\player\Player;
use SkyBlock\Data;
use SkyBlock\Main;
use SkyBlock\user\User;

class CasinoFormFunctions {
    /** @var Main */
    public Main $pl;
    /** @var FormFunctions */
    public FormFunctions $ff;

    public function __construct(Main $plugin, FormFunctions $formFunctions) {
        $this->pl = $plugin;
        $this->ff = $formFunctions;
    }

    public function sendCasinoMainMenu(Player $player) : void {
        $func = function(Player $player, ?int $data) : void {
            if ($data !== null) {
                switch ($data) {
                    case 0:
                        $this->sendFlipACoin($player);
                        break;
                    case 1:
                        $this->sendRollADice($player);
                        break;
                    default:
                        break;
                }
            }
        };
        $this->ff->sendSimpleForm($player, "§4§lCasino", "§6Select a Casino type to play -", ["§3Flip a coin", "§3Roll a dice"], $func);
    }

    public function sendFlipACoin(Player $player) : void {
        $form = new CustomForm(null);
        $form->setTitle("§3Flip A Coin");
        $user = $this->pl->getUserManager()->getOnlineUser($player->getName());
        $chips = $user->getChips();
        $won = $user->getWon();
        $max = Data::$casinoMaxCoinFlipBet;
        $form->addLabel("§fIn this game, you bet heads or tails. If the coin lands on your bet, your money is doubled!");
        $form->addLabel("You have - $chips chips\nCost - " . Data::$casinoChipCostCoinFlip . " chips");
        $form->addDropdown("§3Select a result -", ["Heads", "Tails"], 0);
        $form->addLabel("Casinos won - $won");
        $form->addSlider("§3You are betting - $", Data::$minCasinoBet, $max);
        $form->addLabel("§fGet more casino chips by voting or in crates!");
        $form->setCallable(function(Player $player, ?array $data) use ($user, $max) : void {
            if ($data !== null) {
                $chips = $user->getChips();
                $won = $user->getWon();
                $newWon = $won + 1;
                if ($chips < Data::$casinoChipCostCoinFlip) {
                    $error = "You only have $chips chips, you need " . Data::$casinoChipCostCoinFlip . " casino chips to play this game!\nGet chips from /vote or crates!";
                    $this->sendResultForm($player, $error, "sendCasinoMainMenu");
                    return;
                }
                $bet = (int) $data[4];
                if (!$this->betCheck($user, $bet, $max)) return;
                $chance = mt_rand(1, 100);
                $newChips = $chips - Data::$casinoChipCostCoinFlip;
                $user->removeChips(Data::$casinoChipCostCoinFlip);
                $result = ($chance < 46) ? 'Heads' : 'Tails';
                $bett = (int) $data[2];
                if ($bett == $chance) {
                    $ogmoney = $bet * 2;
                    $user->addWon();
                    $mcboost = $this->pl->getEvFunctions()->mcmmoGamblingBoost($user);
                    $incr = ($mcboost / 100) * $ogmoney;
                    $money = $ogmoney + $incr;
                    $user->addMoney($money);
                    $user->setPoints(15, "gambling");
                    $extra = ($mcboost > 0) ? ' + MCMMO Boost - ' . $incr . "$ (+" . $mcboost . "%)" : '';
                    $info = "Congratulations, the coin landed on $result!\nYou win $bet X 2 = $$ogmoney" . $extra . "! No. of casinos won = $newWon Chips left: $newChips";
                } else {
                    $user->setPoints(mt_rand(5, 10), "gambling");
                    $info = "Unfortunate, the coin landed on $result! Better luck next time.\nYou lost $$bet. Chips left: $newChips";
                }
                $this->sendResultForm($player, $info, "sendCasinoMainMenu");
            }
        }
        );
        $player->sendForm($form);
    }

    public function sendResultForm(Player $player, string $msg, string $funcName, array $args = []) : void {
        $this->ff->sendModalForm($player, "§6Result", $msg, ["§2Go back", "§cExit"], function(Player $player, ?bool $data) use ($funcName, $args) {
            if ($data) {
                assert(method_exists($this, $funcName));
                array_unshift($args, $player);
                call_user_func_array([$this, $funcName], $args);
            }
        }
        );
    }

    public function betCheck(User $user, int $bet, int $max) : bool {
        if (!$user->removeMobCoin($bet)) {
            $error = "You don't have $bet to bet! Check your money with /mymoney";
            $this->sendResultForm($user->getPlayer(), $error, "sendCasinoMainMenu");
            return false;
        }
        if ($bet < Data::$minCasinoBet || $bet > $max) {
            $error = "Session error! Try again!";
            $this->sendResultForm($user->getPlayer(), $error, "sendCasinoMainMenu");
            return false;
        }
        return true;
    }

    public function sendRollADice(Player $player) : void {
        $form = new CustomForm(null);
        $form->setTitle("§3Roll A Dice");
        $user = Main::getInstance()->getUserManager()->getOnlineUser($player->getName());
        $chips = $user->getChips();
        $won = $user->getWon();
        $maxBet = Data::$casinoMaxDiceRollBet;
        $form->addLabel("§fIn This game, you bet on a dices outcome 1-6. If you win, your bet is multiplied by 5!");
        $form->addLabel("You have - $chips chips\nCost - " . Data::$casinoChipCostDiceRoll . " chips");
        $form->addDropdown("§3Select an outcome -", ["1", "2", "3", "4", "5", "6"], 0);
        $form->addLabel("Casinos won - $won");
        $form->addSlider("§3You are betting - $", Data::$minCasinoBet, $maxBet);
        $form->addLabel("§fGet more casino chips through /vote or in crates!");
        $form->setCallable(function(Player $player, ?array $data) use ($user, $maxBet, $chips, $won) : void {
            if ($data !== null) {
                $new = $won + 1;
                if ($chips < Data::$casinoChipCostDiceRoll) {
                    $error = "You only have $chips chips. You need " . Data::$casinoChipCostDiceRoll . " casino chips to play this game!\nGet more casino chips through /vote or in crates!";
                    $this->sendResultForm($player, $error, "sendCasinoMainMenu");
                    return;
                }
                $betAmount = (int) $data[4];
                if (!$this->betCheck($user, $betAmount, $maxBet)) {
                    return;
                }
                $chance = mt_rand(0, 5);
                $newChips = $chips - Data::$casinoChipCostDiceRoll;
                $user->removeChips(Data::$casinoChipCostDiceRoll);
                $res = $chance + 1;
                $betOutcome = (int) $data[2];
                if ($betOutcome === $chance) {
                    $ogMoney = $data[4] * 5;
                    $user->addWon();
                    $mcBoost = Main::getInstance()->getEvFunctions()->mcmmoGamblingBoost($user);
                    $incr = ($mcBoost / 100) * $ogMoney;
                    $money = $ogMoney + $incr;
                    $user->addMoney($money);
                    $user->setPoints(15, "gambling");
                    $extra = ($mcBoost > 0) ? " + MCMMO Boost - $" . $incr . " (+" . $mcBoost . "%)" : "";
                    $info = "Congratulations! The dice landed on $res!\nYou win $betAmount X 5 = $ogMoney" . $extra . "! No. of casinos won = $new Chips left: $newChips";
                } else {
                    $user->setPoints(mt_rand(5, 10), "gambling");
                    $info = "Oh sad, The dice landed on $res! Better luck next time!\nYou lost $betAmount! Chips left: $newChips";
                }
            }
        }
        );
        $player->sendForm($form);
    }
}