<?php

$app['db.options'] = array(
    'driver'    => 'pdo_mysql',
    'charset'   => 'utf8',
    'host'      => '127.0.0.1',
    'port'      => '3306',
    'dbname'    => 'microcms',
    'user'      => 'microcms_user',
    'password'  => 'secret',
);

$app['debug'] = true;

$app['monolog.level'] = 'INFO';