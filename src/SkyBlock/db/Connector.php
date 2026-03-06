<?php

namespace SkyBlock\db;

use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use SkyBlock\Main;

class Connector {

    /** @var ?DataConnector */
    public static ?DataConnector $conn = null;

    /**
     * @param array $config
     */
    public static function init(array $config) : void {
        self::$conn = $connector = libasynql::create(Main::getInstance(), $config, ['mysql' => 'mysql.sql']);

        $connector->executeGeneric("records.init");
        $connector->waitAll();

        RecordDB::init();
    }

    public static function close() : void {
        RecordDB::update();
        if (self::$conn !== null && isset(self::$conn)) {
            self::$conn->waitAll();
            self::$conn->close();
        }
    }

    /**
     * @return DataConnector
     */
    public static function get() : DataConnector {
        return self::$conn;
    }

}