<?php

$app->get('/', function () use($app) {
    $articles = $app['dao.article']->findAll();

    ob_start();
    require '../views/view.php';
    $view = ob_get_clean();
    return $view;
});