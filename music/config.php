<?php
//连接数据库
//header("Content-type:text/html;charset=utf-8");
$dbms='mysql';
$dbName='music';
$user='root';
$pwd='root';
$host='localhost';
$dsn="$dbms:host=$host;dbname=$dbName";
try {
    $pdo=new PDO($dsn,$user,$pwd);
//    echo '数据库连接成功';
}catch (Exception $e){
    echo $e->getMessage()."<br>";
}

