<?php

$app->get('/', function () {
    require '../src/model.php';
    $articles = getArticles();

    ob_start();
    require '../views/view.php';
    $view = ob_get_clean();
    return $view;
});