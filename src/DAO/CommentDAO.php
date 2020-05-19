<?php


namespace MicroCMS\DAO;


use Doctrine\DBAL\Connection;
use MicroCMS\Domain\Comment;

class CommentDAO extends DAO
{
    protected $articleDAO;

    public function setArticleDAO(ArticleDAO $articleDAO)
    {
        $this->articleDAO = $articleDAO;
    }

    public function findAllByArticle($articleId)
    {
        $article = $this->articleDAO->find($articleId);

        $sql = "select id, content, author from comment where article_id = ? order by id";
        $result = $this->getDb()->fetchAll($sql, [$articleId]);

        $comments = [];
        foreach ($result as $row) {
            $commentId = $row['id'];
            $comment = $this->buildDomainObject($row);
            $comment->setArticle($article);
            $comments[$commentId] = $comment;
        }

        return $comments;
    }

    protected function buildDomainObject(array $row)
    {
        $comment = new Comment();

        $comment->setId($row['id']);
        $comment->setContent($row['content']);
        $comment->setAuthor($row['author']);

        if (array_key_exists('article_id', $row)) {
            $articleId = $row['article_id'];
            $article = $this->articleDAO->find($articleId);

            $comment->setArticle($article);
        }

        return $comment;
    }
}