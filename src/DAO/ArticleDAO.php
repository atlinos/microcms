<?php

namespace MicroCMS\DAO;

use Doctrine\DBAL\Connection;
use MicroCMS\Domain\Article;

class ArticleDAO
{
    /**
     * Database connection
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $db;

    /**
     * Constructor
     *
     * @param \Doctrine\DBAL\Connection The database connection object
     */
    public function __construct(Connection $db) {
        $this->db = $db;
    }

    /**
     * Return a list of all articles, sorted by date (most recent first).
     *
     * @return array A list of all articles.
     */
    public function findAll() {
        $sql = "select * from article order by id desc";
        $result = $this->db->fetchAll($sql);

        $articles = array();
        foreach ($result as $row) {
            $articleId = $row['id'];
            $articles[$articleId] = $this->buildArticle($row);
        }

        return $articles;
    }

    /**
     * Creates an Article object based on a DB row.
     *
     * @param array $row The DB row containing Article data.
     * @return \MicroCMS\Domain\Article
     */
    private function buildArticle(array $row) {
        $article = new Article();

        $article->setId($row['id']);
        $article->setTitle($row['title']);
        $article->setContent($row['content']);

        return $article;
    }
}