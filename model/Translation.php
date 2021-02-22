<?php


class Translation extends DatabaseQuery {

    public $id; public $translation_name; public $translation; public $language; public $model; public $model_id;
    public static $tableName = 'translations';
    public static $className = 'Translation';

    public function create($translationName, $translation, $language, $modelId, $model) {

        $this->translation_name = $translationName;
        $this->language = $language;
        $this->translation = $translation;
        $this->model = $model;
        $this->model_id = $modelId;

        Translation::save($this);
    }

    public static function findForModel($model, $modelId, $name, $language) {
        global $db;

        $sql = 'SELECT * FROM ' . static::$tableName . " where model = ? and model_id = ? and translation_name=? and language=?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$model, $modelId, $name, $language]);

        return $stmt->fetchAll(PDO::FETCH_CLASS, static::$className);
    }
    
}