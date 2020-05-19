<?php

$app->get('/', function () use ($app) {
    $articles = $app['dao.article']->findAll();

    return $app['twig']->render('index.html.twig', [
        'articles' => $articles
    ]);
})->bind('home');

$app->get('/article/{id}', function ($id) use ($app) {
    $article = $app['dao.article']->find($id);
    $comments = $app['dao.comment']->findAllByArticle($id);

    return $app['twig']->render('article.html.twig', [
        'article' => $article,
        'comments' => $comments
    ]);
})->bind('article');