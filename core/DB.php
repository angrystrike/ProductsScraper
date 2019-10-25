<?php

namespace core;


use PDO;


class DB
{
    private static $db;

   /* public static function getConnection() {
        if (!(self::$db instanceof PDO)) {
            self::$db = self::setConnection();
        }
        return self::$db;
    }*/

    public static function setConnection($params)
    {
        $connectionString = "mysql:host={$params['host']};dbname={$params['dbname']};charset=utf8";
        return new PDO($connectionString, $params['user'], $params['password']);
    }

    public static function create($table, array $data)
    {
        $columns = implode(',', array_keys($data));
        $values = implode(',', array_fill(0, count($data), '?'));

        $query = self::$db->prepare("INSERT INTO {$table} ({$columns}) VALUES ({$values})");
        $query->execute(array_values($data));

        return self::$db->lastInsertId();
    }

    public static function count($table)
    {
        return self::$db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
    }
}