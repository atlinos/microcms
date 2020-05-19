<?php

namespace MicroCMS\DAO;

use Doctrine\DBAL\Connection;
use MicroCMS\Domain\Article;

class ArticleDAO extends DAO
{
    public function findAll() {
        $sql = "select * from article order by id desc";
        $result = $this->getDb()->fetchAll($sql);

        $articles = array();
        foreach ($result as $row) {
            $articleId = $row['id'];
            $articles[$articleId] = $this->buildDomainObject($row);
        }

        return $articles;
    }

    public function find($id)
    {
        $sql = "select * from article where id = ?";
        $row = $this->getDb()->fetchAssoc($sql, [$id]);

        if ($row) {
            return $this->buildDomainObject($row);
        } else {
            throw new \Exception("No article matching id " . $id);
        }
    }

    protected function buildDomainObject(array $row) {
        $article = new Article();

        $article->setId($row['id']);
        $article->setTitle($row['title']);
        $article->setContent($row['content']);

        return $article;
    }
}