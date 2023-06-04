<?php

namespace ayd1ndemirci\provider;

use ayd1ndemirci\Main;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class SQLiteDatabase
{

    /**
     * @var DataConnector
     */
    private DataConnector $database;

    public function __construct() {
        $this->database = libasynql::create(
            Main::getInstance(),
            Main::getInstance()->getConfig()->get("database"),
            ["sqlite" => "database/sqlite.sql"]
        );
        $this->database->executeGeneric("totem.createTable");
    }

    public function addPlayer(string $playerName):void
    {
      $this->database->executeInsert("totem.addPlayer", [
          "playerName" => $playerName
      ]);
    }

    public function updateToken(string $playerName, int $addedTokenCount, \Closure $callbackFn) :void {
        $this->database->executeChange("totem.updateTotem", [
            "playerName" => $playerName,
            "totemCount" => $addedTokenCount
        ], function() use($callbackFn) :void {
            $callbackFn();
        });
    }

    public function getPlayerToken(string $playerName, \Closure $callbackFn) :void {
        $this->database->executeSelect("totem.getTotem", [
            "playerName" => $playerName
        ], function (array $rows) use($callbackFn) :void {
            $callbackFn($rows[0]["totemCount"]);
        });
    }

    /**
     * @return DataConnector
     */
    public function getDataConnector() :DataConnector {
        return $this->database;
    }
}