<?php


namespace MicroCMS\Controller;


use MicroCMS\Domain\Article;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class ApiController
{
    public function getArticlesAction(Application $app)
    {
        $articles = $app['dao.article']->findAll();

        $responseData = [];
        foreach ($articles as $article) {
            $responseData[] = $this->buildArticleArray($article);
        }

        return $app->json($responseData);
    }

    public function getArticleAction($id, Application $app)
    {
        $article = $app['dao.article']->find($id);
        $responseData = $this->buildArticleArray($article);

        return $app->json($responseData);
    }

    public function addArticleAction(Request $request, Application $app)
    {
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

        $responseData = $this->buildArticleArray($article);

        return $app->json($responseData, 201);
    }

    public function deleteArticleAction($id, Application $app)
    {
        $app['dao.comment']->deleteAllByArticle($id);
        $app['dao.article']->delete($id);

        return $app->json('No Content', 204);
    }

    protected function buildArticleArray(Article $article)
    {
        return [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'content' => $article->getContent()
        ];
    }
}