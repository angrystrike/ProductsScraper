<?php

namespace core;


use PDO;


class DB
{
    private static $db;

    public static function getConnection() {
        if (!(self::$db instanceof PDO)) {
            self::$db = self::setConnection();
        }
        return self::$db;
    }

    private static function setConnection()
    {
        $params = require __DIR__ . '/../config/params.php';
        $connectionString = "mysql:host={$params['host']};dbname={$params['dbname']};charset=utf8";

        return new PDO($connectionString, $params['user'], $params['password']);
    }

    public static function create($table, array $data)
    {
        $columns = implode(',', array_keys($data));
        $values = '';

        foreach (array_values($data) as $value) {
            $values .= '?,';
        }
        $values = trim($values, ',');

        $realValues = array_values($data);

        $query = self::getConnection()->prepare("INSERT INTO {$table} ({$columns}) VALUES ({$values})");
        $query->execute($realValues);

        return self::$db->lastInsertId();
    }

    public static function count($table)
    {
        return self::getConnection()->query("SELECT COUNT(*) FROM $table")->fetchColumn();
    }
}