<?php

namespace GeoFixer\models;

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;;
use Exception;

/**
 * Class DatabaseConnection
 *
 * @package GeoFixer\models
 */
class DatabaseConnection
{
    /**
     * @var
     */
    protected static $connection;

    /**
     * DatabaseConnection constructor.
     */
    public function __construct()
    {
       self::makeConnection();
    }

    /**
     * Создаем подключение к БД
     *
     * @param null $config
     * @return Connection
     * @throws Exception
     */
    public static function makeConnection($config = null)
    {
        if (!$config) {
            $config = include dirname(dirname(__FILE__)) . '/config/database.php';
        }

        $driver = new Mysql($config);
        self::$connection = new Connection([
            'driver' => $driver
        ]);

        if (self::$connection === false) {
            throw new Exception('Database connection error');
        }

        return self::$connection;
    }

    /**
     * Возвращаем соединение, если существует, иначе создаем и возвращаем
     *
     * @return Connection
     */
    public static function connection()
    {
        return self::$connection ? self::$connection : self::makeConnection();
    }

}
