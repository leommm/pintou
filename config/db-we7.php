<?php

function db_we7()
{
//    require __DIR__ . '/../../../../data/config.php';
//
//    $host = $config['db']['master']['host'] ?: $config['db']['host'];
//    $port = $config['db']['master']['port'] ?: $config['db']['port'];
//    $database = $config['db']['master']['database'] ?: $config['db']['database'];
//    $username = $config['db']['master']['username'] ?: $config['db']['username'];
//    $password = $config['db']['master']['password'] ?: $config['db']['password'];
//    var_dump($username);exit;
//    return [
//        'class' => 'yii\db\Connection',
//        'dsn' => 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $database,
//        'username' => $username,
//        'password' => $password,
//        'charset' => 'utf8',
//        'tablePrefix' => 'hjmall_',
//    ];
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=39.106.214.242;port=3306;dbname=xs_cshop',
        'username' =>'root',
        'password' => "959245",
        'charset' => 'utf8',
        'tablePrefix' => 'cshopmall_',
    ];
}

return db_we7();