<?php

class Database {

    public $config;

    public function __construct($config) {
        $this->config = $config;
    }

    public function connection () {

        $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
             return new PDO($dsn, $this->config['username'], $this->config['password'], $options);
        } catch (\PDOException $e) {
             throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}