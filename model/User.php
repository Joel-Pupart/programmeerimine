<?php

class User extends DatabaseQuery {

    public $id;
    public $email;
    public $password;
    public static $tableName = 'users';
    public static $className = 'User';
    public $role;

    public function info () {
        return $this->email . '(' . $this->id . ')';
    }

    public static function findByEmail($email) {
        global $db;

        $sql = 'SELECT * FROM ' . static::$tableName . " where email=? LIMIT 1";
        
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, static::$className);
        $stmt->execute([$email]);

        return $stmt->fetch();
    }

    public static function all($start = 5, $end = 5, $search = "", $auth = "") {
        global $db;

        $sql = 'SELECT * FROM ' . static::$tableName;

        $execute = [];
        if (!empty($search)) {
            $search = "%{$search}%";
            $sql .= ' where email LIKE ?';
            $execute[] = $search;
        }
    
        if ($start === 0 && $end === 0) {
            $sql .= ' ORDER BY id DESC LIMIT 10';
        } else {
            $sql .= ' ORDER BY id DESC LIMIT ?, ?';
            $execute[] = $start;
            $execute[] = $end;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($execute);
        return $stmt->fetchAll();

    }

}