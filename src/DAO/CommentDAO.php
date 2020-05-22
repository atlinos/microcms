<?php


namespace MicroCMS\DAO;


use MicroCMS\Domain\Comment;

class CommentDAO extends DAO
{
    protected $articleDAO;

    protected $userDAO;

    public function setArticleDAO(ArticleDAO $articleDAO)
    {
        $this->articleDAO = $articleDAO;
    }

    public function setUserDAO(UserDAO $userDAO)
    {
        $this->userDAO = $userDAO;
    }

    public function save(Comment $comment)
    {
        $commentData = [
            'content' => $comment->getContent(),
            'article_id' => $comment->getArticle()->getId(),
            'user_id' => $comment->getAuthor()->getId()
        ];

        if ($comment->getId()) {
            $this->getDb()->update('comment', $commentData, ['id' => $comment->getId()]);
        } else {
            $this->getDb()->insert('comment', $commentData);

            $id = $this->getDb()->lastInsertId();
            $comment->setId($id);
        }
    }

    public function find($id)
    {
        $sql = "select * from comment where id = ?";
        $row = $this->getDb()->fetchAssoc($sql, [$id]);

        if ($row) {
            return $this->buildDomainObject($row);
        } else {
            throw new \Exception("No comment matching id " . $id);
        }
    }

    public function findAll() {
        $sql = "select * from comment order by id desc";
        $result = $this->getDb()->fetchAll($sql);

        $entities = array();
        foreach ($result as $row) {
            $id = $row['id'];
            $entities[$id] = $this->buildDomainObject($row);
        }

        return $entities;
    }

    public function findAllByArticle($articleId)
    {
        $article = $this->articleDAO->find($articleId);

        $sql = "select id, content, user_id from comment where article_id = ? order by id";
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

    public function delete($id)
    {
        $this->getDb()->delete('comment', ['id' => $id]);
    }

    public function deleteAllByArticle($articleId)
    {
        $this->getDb()->delete('comment', ['article_id' => $articleId]);
    }

    public function deleteAllByUser($userId)
    {
        $this->getDb()->delete('comment', ['user_id' => $userId]);
    }

    protected function buildDomainObject(array $row)
    {
        $comment = new Comment();

        $comment->setId($row['id']);
        $comment->setContent($row['content']);

        if (array_key_exists('article_id', $row)) {
            $articleId = $row['article_id'];
            $article = $this->articleDAO->find($articleId);

            $comment->setArticle($article);
        }

        if (array_key_exists('user_id', $row)) {
            $userId = $row['user_id'];
            $user = $this->userDAO->find($userId);

            $comment->setAuthor($user);
        }

        return $comment;
    }
}