<?php


namespace MicroCMS\Controller;


use MicroCMS\Domain\Comment;
use MicroCMS\Form\Type\CommentType;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class HomeController
{
    public function indexAction(Application $app)
    {
        $articles = $app['dao.article']->findAll();

        return $app['twig']->render('index.html.twig', [
            'articles' => $articles
        ]);
    }

    public function articleAction($id, Request $request, Application $app)
    {
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
    }

    public function loginAction(Request $request, Application $app)
    {
        return $app['twig']->render('login.html.twig', [
            'error' => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username')
        ]);
    }
}