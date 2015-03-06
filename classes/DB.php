<?php

class DB {
    private $dbh;
    private $className = 'stdClass';

    public function __construct() {
        $this->dbh = new PDO('mysql:dbname=lesson01;host=localhost', 'root', '');
    }

    public function setClassName($className) {
        $this->className = $className;
    }

    public function query($sql, $params=[], $isReturnable) {
        $sth = $this->dbh->prepare($sql);
        $res = $sth->execute($params);
        if (true === $isReturnable) {
            return $sth->fetchAll(PDO::FETCH_CLASS, $this->className);
        }
        return $res;
    }

    public function lastInsertId() {
        return $this->dbh->lastInsertId();
    }

    //public function execute($sql, $params=[]) {
    //    $sth = $this->dbh->prepare($sql);
    //    return $sth->execute($params);
    //}
}