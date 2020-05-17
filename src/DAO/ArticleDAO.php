<?php

namespace MicroCMS\DAO;

use Doctrine\DBAL\Connection;
use MicroCMS\Domain\Article;

class ArticleDAO
{
    protected $db;

    /**
     * ArticleDAO constructor.
     * @param $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $sql = 'select * from article order by id desc';
        $result = $this->db->fetchAll($sql);

        $articles = [];
        foreach ($result as $row) {
            $articleId = $row['id'];
            $articles[$articleId] = $this->buildArticle($row);
        }

        return $articles;
    }

    protected function buildArticle(array $row)
    {
        $article = new Article();

        $article->setId($row['id']);
        $article->setTitle($row['title']);
        $article->setContent($row['content']);

        return $article;
    }
}