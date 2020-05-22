<?php

namespace MicroCMS\DAO;

use Doctrine\DBAL\Connection;
use MicroCMS\Domain\Article;

class ArticleDAO extends DAO
{
    public function findAll() {
        $sql = "select * from article order by id desc";
        $result = $this->getDb()->fetchAll($sql);

        $entities = array();
        foreach ($result as $row) {
            $id = $row['id'];
            $entities[$id] = $this->buildDomainObject($row);
        }

        return $entities;
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

    public function save(Article $article)
    {
        $articleData = [
            'title' => $article->getTitle(),
            'content' => $article->getContent()
        ];

        if ($article->getId()) {
            $this->getDb()->update('article', $articleData, ['id' => $article->getId()]);
        } else {
            $this->getDb()->insert('article', $articleData);

            $id = $this->getDb()->lastInsertId();
            $article->setId($id);
        }
    }

    public function delete($id)
    {
        $this->getDb()->delete('article', ['id' => $id]);
    }

    protected function buildDomainObject(array $row) {
        $article = new Article();

        $article->setId($row['id']);
        $article->setTitle($row['title']);
        $article->setContent($row['content']);

        return $article;
    }
}