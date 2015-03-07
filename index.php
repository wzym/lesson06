<?php
require_once __DIR__ . '/autoload.php';

$ctrl = isset($_GET['ctrl']) ? $_GET['ctrl'] : 'News';
$ctrl .= 'Controller';
$act = isset($_GET['act']) ? $_GET['act'] : 'ShowAll';
$act = 'action' . $act;

$controller = new $ctrl;
try {
    $controller->$act();
} catch (E404Exception $exc) {
    http_response_code(404);
    $controller->actionShowError($exc->getMessage());
}

