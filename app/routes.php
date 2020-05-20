<?php

use Symfony\Component\HttpFoundation\Request;

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

$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render('login.html.twig', [
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username')
    ]);
})->bind('login');