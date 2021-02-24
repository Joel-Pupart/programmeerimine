<?php

class DatabaseQuery {

    public static $tableName;
    public static $className;

    public static function all() {
        global $db;

        $sql = 'SELECT * FROM ' . static::$tableName;

        return $db->query($sql)->fetchAll(PDO::FETCH_CLASS, static::$className);
    }

    public static function count() {
        global $db;

        $sql = 'SELECT COUNT(*) as "count" FROM ' . static::$tableName;

        $array = $db->query($sql)->fetchAll(PDO::FETCH_CLASS, static::$className);
        $request = reset($array);

        return $request->count;
    }

    public static function findById($id) {
        global $db;

        $sql = 'SELECT * FROM ' . static::$tableName . " where id=? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, static::$className);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function save($obj) {

        if (empty($obj->id)) {
            return self::insert($obj);
        } else {
            return self::update($obj);
        }
    }

    public static function insert($obj) {
        global $db;

        $array = get_object_vars($obj);
        unset($array['id']);

        $arrayKeys = array_keys($array);
        $arrayValues = array_values($array);
        $arrayValuePlaceHolders = [];

        for ($i = 0; $i < count($arrayKeys); $i++) {
            $arrayValuePlaceHolders[] = '?';
        }

        $sql = 'INSERT INTO ' . static::$tableName;

        $sql.= ' (`' . join('`,`', $arrayKeys) .'`)';
        $sql.= ' VALUES ';
        $sql.= '(' . join(',', $arrayValuePlaceHolders) .')';
        
        try {
            $db->prepare($sql)->execute($arrayValues);
        } catch (PDOException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }

        return [
            'status' => true,
            'message' => 'success',
            'id' => $db->lastInsertId()
        ];

    }

    public static function update($obj) {

        global $db;

        $array = get_object_vars($obj);
        unset($array['id']);

        $sql = 'UPDATE ' . static::$tableName . ' SET ';

        $arrayKeys = array_keys($array);
        $arrayValues = array_values($array);
        $arrayValuePlaceHolders = [];

        foreach ($arrayKeys as $key) {
            $arrayValuePlaceHolders[] = "`" . $key . "`" . ' = ?';
        }

        $sql.= join(",", $arrayValuePlaceHolders);

        $sql.= ' WHERE id = ?';

        $arrayValues[] = $obj->id;

        try {
            $db->prepare($sql)->execute($arrayValues);
        } catch (PDOException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }

        return [
            'status' => true,
            'message' => t('success'),
        ];

    }

    public static function delete($obj, $img = false) {

        global $db;

        if (empty($obj->id)) {
            return t('object_missing');
        }
        
        $sql = 'DELETE FROM ' . static::$tableName . ' WHERE id=?';

        $stmt = $db->prepare($sql);
        $stmt->execute([$obj->id]);
        if ($stmt->rowCount() > 0) {
            if ($img) {
                deleteImage($obj);
            }
            return t('deleted');
        }

        return t('delete_failed');

    }
}