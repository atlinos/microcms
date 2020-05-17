<?php

$app->get('/', function () use($app) {
    $articles = $app['dao.article']->findAll();

    return $app['twig']->render('index.html.twig', ['articles' => $articles]);
});