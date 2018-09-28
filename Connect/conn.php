<?php
/**
 * Created by PhpStorm.
 * User: luosilent
 * Date: 2018/9/20
 * Time: 16:53
 */
error_reporting(0);
function conn()
{
    $driver = 'mysql';
    $dbName = 'chat';
    $host    = 'localhost';
    $charset = 'utf8';
    $dsn = "$driver:host=$host;dbName=$dbName;charset=$charset";
    $uName = "root";
    $pWord = "root";

    try {
        $conn = new PDO($dsn, $uName, $pWord);
        $conn->query("set NAMES $charset");
        //添加使用数据库
        $str = "use $dbName";
        $conn->exec($str);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        $error = "Access denied! ";
        $ext = $e->getMessage();
        echo $error . $ext;
    }

    return $conn;

}


function getUser($name)
{
    $conn = conn();
    $user = array();

    $sql = "select * from member where username = :username";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(":username", $name);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $user = $row;
    }

    return $user;
}