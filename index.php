<?php

class Card
{
    public $suit;
    public $value;
    public $intValue;

    function __construct($suit, $value, $intValue)
    {
        $this->suit = $suit;
        $this->value = $value;
        $this->intValue = $intValue;
    }

    public function showCardInfo()
    {
        return $this->suit . $this->value . "(" . strval($this->intValue) . ")" . PHP_EOL;
    }
}

class Deck
{
    function __construct()
    {
        $this->deck = Deck::createDeck();
    }

    public static function createDeck()
    {
        $newDeck = [];
        $suits = ["♣", "♦", "♥", "♠"];
        $values = ["A", "2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K"];

        for ($i = 0; $i < count($suits); $i++) {
            for ($j = 0; $j < count($values); $j++) {
                array_push($newDeck, new Card($suits[$i], $values[$j], $j + 1));
            }
        }
        return $newDeck;
    }

    public function draw()
    {
        return array_pop($this->deck);
    }

    public function shuffleDeck()
    {
        for ($i = 0; $i < count($this->deck); $i++) {
            $j = rand(0, count($this->deck) - 1);
            $temp = $this->deck[$i];
            $this->deck[$i] = $this->deck[$j];
            $this->deck[$j] = $temp;
        }
    }
}

class Dealer
{
    public static function startGame($amountOfPlayers)
    {
        $table = [
            "players" => [],
            "deck" => new Deck()
        ];

        $table["deck"]->shuffleDeck();

        for ($i = 0; $i < $amountOfPlayers; $i++) {
            $playerCard = [];
            for ($j = 0; $j < 5; $j++) {
                array_push($playerCard, $table["deck"]->draw());
            }
            array_push($table["players"], $playerCard);
        }
        return $table;
    }

    public static function printTableInfo($table)
    {
        echo "Amount of players : " . count($table["players"]) . PHP_EOL . "At this table : " . PHP_EOL;
        foreach ($table["players"] as $k => $v) {
            echo strval($k + 1) . "player's cards : " . PHP_EOL;
            for ($i = 0; $i < count($v); $i++) {
                echo $v[$i]->showCardInfo();
            }
        }
    }

    public static function winnerPairOfCards($table)
    {
        $hashMap1 = Caluculate::cashing(Caluculate::makeNumberArray($table["players"][0]));
        $hashMap2 = Caluculate::cashing(Caluculate::makeNumberArray($table["players"][1]));

        $maxPair1 = Caluculate::maxValue($hashMap1);
        $maxPair2 = Caluculate::maxValue($hashMap2);

        if ($maxPair1["countPair"] > $maxPair2["countPair"]) return "player1";
        if ($maxPair1["countPair"] < $maxPair2["countPair"]) return "player2";

        if ($maxPair1["highValue"] > $maxPair2["highValue"]) return "player1";
        if ($maxPair2["highValue"] > $maxPair1["highValue"]) return "player2";

        return "draw";
    }
}
class Caluculate
{
    public static function cashing(array $arr): array
    {
        $hashMap = [];
        for ($i = 0; $i < count($arr); $i++) {
            if (is_null($hashMap[$arr[$i]])) $hashMap[$arr[$i]] = 1;
            else $hashMap[$arr[$i]] += 1;
        }
        return $hashMap;
    }

    public static function maxValue(array $hashMap): array
    {
        $result = [
            "countPair" => 0,
            "highValue" => ""
        ];
        foreach ($hashMap as $key => $value) {
            if ($result["countPair"] < $value) {
                $result["countPair"] = $value;
                $result["highValue"] = $key;
            }
        }
        return $result;
    }

    public static function makeNumberArray($cardList): array
    {
        $arr = [];
        for ($i = 0; $i < count($cardList); $i++) {
            array_push($arr, $cardList[$i]->intValue);
        }
        return $arr;
    }
}

$table1 = Dealer::startGame(2);
Dealer::printTableInfo($table1);
print_r(Dealer::winnerPairOfCards($table1));