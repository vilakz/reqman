<?php

namespace common\components;

use yii\db\Connection;

/**
 * Получение данных о текущей БД.
 * В частности из-за разного поведения для timestamp(6), DEFAULT, ON UPDATE
 */
class DbEngineInfo
{

    /**
     * База MySQL
     */
    const DB_MYSQL = 'mysql';

    /**
     * База MariaDB
     */
    const DB_MARIADB = 'mariadb';

    /**
     * @var null | string Закешированное значение
     */
    public static $current = null;

    /**
     * Получить название БД. Пока MySQL и MariaDB
     * @param $db Connection Соединение для БД
     * @return string
     */
    public static function getDbMainName($db)
    {
        if (is_null(static::$current)) {
            $res = $db->createCommand("SHOW VARIABLES LIKE 'version_comment'")->queryOne();
            if (isset($res['Value']) && false !== stripos($res['Value'], 'maria')) {
                static::$current = static::DB_MARIADB;
            } else {
                static::$current = static::DB_MYSQL;
            }
        }
        return static::$current;
    }

    /**
     * Поулчить подходящее CURRENT_TIMESTAMP с микросекундами.
     * @param $db Connection
     * @return string
     */
    public static function getCurrentTimestamp6($db)
    {
        $ret = "CURRENT_TIMESTAMP(6)";
        switch(static::getDbMainName($db))
        {
            case static::DB_MARIADB:
                $ret = "CURRENT_TIMESTAMP";
                break;
        }
        return $ret;
    }

}