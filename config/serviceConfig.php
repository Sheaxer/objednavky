<?php

require_once (__DIR__ . "/../services/OrderService.php");
require_once (__DIR__ . "/../services/mysql/OrderServiceMysqlImpl.php");
require_once ("DatabaseConfig.php");
// ziskanie konfiguracia databazy
function getConnection():?PDO
{
    $conf = new DatabaseConfig();
    return $conf->getConnection();
}

// ziskanie implmentacie service
function getOrderServiceImpl()
{
    $conn = getConnection();
    if($conn == null)
        return null;
    return new OrderServiceMysqlImpl($conn);
}
