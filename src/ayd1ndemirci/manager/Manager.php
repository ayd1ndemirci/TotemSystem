<?php

namespace ayd1ndemirci\manager;

use ayd1ndemirci\Main;
use ayd1ndemirci\provider\SQLiteDatabase;
use SOFe\AwaitGenerator\Await;

class Manager
{
    public SQLiteDatabase $database;

    public function __construct()
    {
        $this->database = Main::getInstance()->getDatabase();
    }

    public function getPlayerTotem(string $playerName) :int
    {
        $totem = 0;
        Await::f2c(function () use(&$totem, $playerName) {
            $rows = (array) yield from $this->database->getPlayerToken($playerName);
            $totem = $rows[0]["totemCount"];
        });
        $this->database->getDataConnector()->waitAll();
        return $totem;
    }
    public function addPlayerTotem(string $playerName, int $amount) :void
    {
        $totem = $this->getPlayerTotem($playerName);
        $this->database->updateToken($playerName, $amount, function (){});
    }
    public function takePlayerTotem(string $playerName, int $amount) :void
    {
        $totem = $this->getPlayerTotem($playerName);
        $this->database->updateToken($playerName, -$amount, function (){});
    }
}
