<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Request;

ErrorHandler::register();
ExceptionHandler::register();

$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../views',
]);
$app->register(new Silex\Provider\AssetServiceProvider(), [
    'assets.version' => 'v1'
]);
$app['twig'] = $app->extend('twig', function (Twig_Environment $twig, $app) {
    $twig->addExtension(new Twig_Extensions_Extension_Text());

    return $twig;
});
$app->register(new Silex\Provider\ValidatorServiceProvider());
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
    ],
    'security.role_hierarchy' => [
        'ROLE_ADMIN' => ['ROLE_USER']
    ],
    'security.access_rules' => [
        ['^/admin', 'ROLE_ADMIN']
    ]
]);
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());
$app->register(new Silex\Provider\MonologServiceProvider(), [
    'monolog.logfile' => __DIR__ . '/../var/logs/microcms.log',
    'monolog.name' => 'MicroCMS',
    'monolog.level' => $app['monolog.level']
]);

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

$app->error(function (Exception $e, Request $request, $code) use ($app) {
    switch ($code) {
        case 403:
            $message = 'Access denied.';
            break;
        case 404:
            $message = 'The requested resource could not be found.';
            break;
        default:
            $message = 'Something went wrong.';
    }

    return $app['twig']->render('error.html.twig', ['message' => $message]);
});

$app->before(function (Request $request) {
    if (strpos($request->headers->get('Content-Type'), 'application/json') === 0) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : []);
    }
});