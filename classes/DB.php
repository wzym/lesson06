<?php

class DB {
    private $dbh;
    private $className = 'stdClass';

    public function __construct() {
        try{
            $this->dbh = new PDO('mysql:dbname=lesson01;host=localhost', 'root', '');
        } catch (PDOException $e) {
            $this->saveLog($e);
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
            $this->saveLog($errPDO);
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

    private function saveLog($err) {
        $file = $err->getFile();
        $lineNum = $err->getLine();
        $message = $err->getMessage();
        $time = date('Y/m/d H:m');
        $log = new EventLog($file, $lineNum, $time, $message);
    }
}