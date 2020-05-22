<?php

use MicroCMS\Domain\Article;
use MicroCMS\Domain\Comment;
use MicroCMS\Domain\User;
use MicroCMS\Form\Type\ArticleType;
use MicroCMS\Form\Type\CommentType;
use MicroCMS\Form\Type\UserType;
use Symfony\Component\HttpFoundation\Request;

$app->get('/', function () use ($app) {
    $articles = $app['dao.article']->findAll();

    return $app['twig']->render('index.html.twig', [
        'articles' => $articles
    ]);
})->bind('home');

$app->match('/article/{id}', function ($id, Request $request) use ($app) {
    $article = $app['dao.article']->find($id);
    $commentFormView = null;

    if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
        $comment = new Comment();

        $comment->setArticle($article);
        $user = $app['user'];
        $comment->setAuthor($user);

        $commentForm = $app['form.factory']->create(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $app['dao.comment']->save($comment);
            $app['session']->getFlashBag()->add('success', 'Your comment was successfully added.');
        }

        $commentFormView = $commentForm->createView();
    }

    $comments = $app['dao.comment']->findAllByArticle($id);

    return $app['twig']->render('article.html.twig', [
        'article' => $article,
        'comments' => $comments,
        'commentForm' => $commentFormView
    ]);
})->bind('article');

$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render('login.html.twig', [
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username')
    ]);
})->bind('login');

$app->get('/admin', function () use ($app) {
    $articles = $app['dao.article']->findAll();
    $comments = $app['dao.comment']->findAll();
    $users = $app['dao.user']->findAll();

    return $app['twig']->render('admin.html.twig', [
        'articles' => $articles,
        'comments' => $comments,
        'users' => $users
    ]);
})->bind('admin');

$app->match('/admin/article/add', function (Request $request) use ($app) {
    $article = new Article();
    $articleForm = $app['form.factory']->create(ArticleType::class, $article);
    $articleForm->handleRequest($request);

    if ($articleForm->isSubmitted() && $articleForm->isValid()) {
        $app['dao.article']->save($article);
        $app['session']->getFlashBag()->add('success', 'The article was successfully created.');
    }

    return $app['twig']->render('article_form.html.twig', [
        'title' => 'New article',
        'articleForm' => $articleForm->createView()
    ]);
})->bind('admin_article_add');

$app->match('/admin/article/{id}/edit', function ($id, Request $request) use ($app) {
    $article = $app['dao.article']->find($id);
    $articleForm = $app['form.factory']->create(ArticleType::class, $article);
    $articleForm->handleRequest($request);

    if ($articleForm->isSubmitted() && $articleForm->isValid()) {
        $app['dao.article']->save($article);
        $app['session']->getFlashBag()->add('success', 'The article was successfully updated.');
    }

    return $app['twig']->render('article_form.html.twig', [
        'title' => 'Edit article',
        'articleForm' => $articleForm->createView()
    ]);
})->bind('admin_article_edit');

$app->get('/admin/article/{id}/delete', function ($id, Request $request) use ($app) {
    $app['dao.comment']->deleteAllByArticle($id);

    $app['dao.article']->delete($id);
    $app['session']->getFlashBag()->add('success', 'The article was successfully removed.');

    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_article_delete');

$app->match('/admin/comment/{id}/edit', function ($id, Request $request) use ($app) {
    $comment = $app['dao.comment']->find($id);
    $commentForm = $app['form.factory']->create(CommentType::class, $comment);
    $commentForm->handleRequest($request);

    if ($commentForm->isSubmitted() && $commentForm->isValid()) {
        $app['dao.comment']->save($comment);
        $app['session']->getFlashBag()->add('success', 'The comment was successfully updated.');
    }

    return $app['twig']->render('comment_form.html.twig', [
        'title' => 'Edit comment',
        'commentForm' => $commentForm->createView()
    ]);
})->bind('admin_comment_edit');

$app->get('/admin/comment/{id}/delete', function ($id, Request $request) use ($app) {
    $app['dao.comment']->delete($id);
    $app['session']->getFlashBag()->add('success', 'The comment was successfully removed.');

    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_comment_delete');

$app->match('/admin/user/add', function (Request $request) use ($app) {
    $user = new User();
    $userForm = $app['form.factory']->create(UserType::class, $user);
    $userForm->handleRequest($request);

    if ($userForm->isSubmitted() && $userForm->isValid()) {
        $salt = substr(md5(time()), 0, 23);
        $user->setSalt($salt);
        $plainPassword = $user->getPassword();

        $encoder = $app['security.encoder.bcrypt'];

        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password);
        $app['dao.user']->save($user);
        $app['session']->getFlashBag()->add('success', 'The user was successfully created.');
    }

    return $app['twig']->render('user_form.html.twig', [
        'title' => 'New user',
        'userForm' => $userForm->createView()
    ]);
})->bind('admin_user_add');

$app->match('/admin/user/{id}/edit', function ($id, Request $request) use ($app) {
    $user = $app['dao.user']->find($id);
    $userForm = $app['form.factory']->create(UserType::class, $user);
    $userForm->handleRequest($request);

    if ($userForm->isSubmitted() && $userForm->isValid()) {
        $plainPassword = $user->getPassword();

        $encoder = $app['security.encoder_factory']->getEncoder($user);

        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password);
        $app['dao.user']->save($user);
        $app['session']->getFlashBag()->add('success', 'The user was successfully updated.');
    }

    return $app['twig']->render('user_form.html.twig', [
        'title' => 'Edit user',
        'userForm' => $userForm->createView()
    ]);
})->bind('admin_user_edit');

$app->get('/admin/user/{id}/delete', function ($id, Request $request) use ($app) {
    $app['dao.comment']->deleteAllByUser($id);

    $app['dao.user']->delete($id);
    $app['session']->getFlashBag()->add('success', 'The user was successfully removed.');

    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_user_delete');

$app->get('/api/articles', function () use ($app) {
    $articles = $app['dao.article']->findAll();

    $responseData = [];
    foreach ($articles as $article) {
        $responseData[] = [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'content' => $article->getContent()
        ];
    }

    return $app->json($responseData);
})->bind('api_articles');

$app->get('/api/article/{id}', function ($id) use ($app) {
    $article = $app['dao.article']->find($id);

    $responseData = [
        'id' => $article->getId(),
        'title' => $article->getTitle(),
        'content' => $article->getContent()
    ];

    return $app->json($responseData);
})->bind('api_article');

$app->post('/api/article', function (Request $request) use ($app) {
    if (! $request->request->has('title')) {
        return $app->json('Missing required parameter: title', 400);
    }
    if (! $request->request->has('content')) {
        return $app->json('Missing required parameter: content', 400);
    }

    $article = new Article();
    $article->setTitle($request->request->get('title'));
    $article->setContent($request->request->get('content'));
    $app['dao.article']->save($article);

    $responseData = [
        'id' => $article->getId(),
        'title' => $article->getTitle(),
        'content' => $article->getContent()
    ];

    return $app->json($responseData, 201);
})->bind('api_article_add');

$app->delete('/api/article/{id}', function ($id, Request $request) use ($app) {
    $app['dao.comment']->deleteAllByArticle($id);
    $app['dao.article']->delete($id);

    return $app->json('No Content', 204);
})->bind('api_article_delete');