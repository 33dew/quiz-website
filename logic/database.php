<?php

namespace Database;
use PDO;
use PDOException;


class DB{
    public static function query($sql, $params = []){
        $json = include "./config.php";
        try {
            $pdo = new PDO("mysql:host=".$json['HOST'].";charset=utf8;port=".$json['PORT'].";dbname=".$json['DBNAME'], $json['USER'], $json['PASSWORD']);
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            echo 'DB ERROR: ' . $e->getMessage();
            die();
        }
    }
}