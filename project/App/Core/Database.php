<?php


namespace App\Core;


class Database
{
    private $host;
    private $username;
    private $password;
    private $dbname;
    private $charset;

    private static $connection;

    public static function getConnection()
    {
        if(empty(self::$connection))
        {
            $db = new Database();
            self::$connection = $db->connect();
        }

        return self::$connection;
    }

    public function connect()
    {
        $this->host = 'mysql';
        $this->username = 'root';
        $this->password = 'secret';
        $this->dbname = 'subscription';
        $this->charset = 'utf8mb4';

        try {
            $dsn = "mysql:host=".$this->host.";charset=".$this->charset."";
            $pdo = new \PDO($dsn, $this->username, $this->password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $pdo->query("CREATE DATABASE IF NOT EXISTS $this->dbname");
            $pdo->query("use $this->dbname");
        }catch (\Exception $e) {
            die('Connection failed: '.$e->getMessage());
        }

        //create database if it does not exists already on connection
        $pdo->query('CREATE TABLE IF NOT EXISTS `subscription` (
                                `id` INT(11) NOT NULL AUTO_INCREMENT,
                                `email` VARCHAR(50) NULL DEFAULT NULL,
                                `subscribed` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
                                PRIMARY KEY (`id`)
                            );
        ');

        return $pdo;
    }

}