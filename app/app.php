<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

ErrorHandler::register();
ExceptionHandler::register();

$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../views',
]);
$app->register(new Silex\Provider\AssetServiceProvider(), [
    'assets.version' => 'v1'
]);
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider(), [
    'security.firewalls' => [
        'secured' => [
            'pattern' => '^/',
            'anonymous' => true,
            'logout' => true,
            'form' => ['login_path' => '/login', 'check_path' => '/login_check'],
            'users' => function () use ($app) {
                return new MicroCMS\DAO\UserDAO($app['db']);
            }
        ]
    ]
]);
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());

$app['dao.article'] = function ($app) {
    return new MicroCMS\DAO\ArticleDAO($app['db']);
};
$app['dao.user'] = function ($app) {
    return new MicroCMS\DAO\UserDAO($app['db']);
};
$app['dao.comment'] = function ($app) {
    $commentDAO = new MicroCMS\DAO\CommentDAO($app['db']);
    $commentDAO->setArticleDAO($app['dao.article']);
    $commentDAO->setUserDAO($app['dao.user']);

    return $commentDAO;
};