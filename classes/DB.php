<?php

class DB {
    private $dbh;
    private $className = 'stdClass';

    public function __construct() {
        try{
            $this->dbh = new PDO('mysql:dbname=lesson010;host=localhost', 'root', '');
        } catch (PDOException $e) {
            $logText = var_dump($e);
            echo $logText;
            $exc403 = new E403Exception('Соединиться с БД не удалось. В остальном всё работает как часы.');
            throw $exc403;
        }

    }

    public function setClassName($className) {
        $this->className = $className;
    }

    public function query($sql, $params=[], $isReturnable) {
        $sth = $this->dbh->prepare($sql);

        try {
            $res = $sth->execute($params);
        } catch (PDOException $errPDO) {
            $exc403 = new E403Exception('Удалось даже соединиться, но запрос оказался некорректным.');
            throw $exc403;
        }
        if (true === $isReturnable) {
            return $sth->fetchAll(PDO::FETCH_CLASS, $this->className);
        }
        return $res;
    }

    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }
}