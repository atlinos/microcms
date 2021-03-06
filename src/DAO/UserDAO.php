<?php


namespace MicroCMS\DAO;


use MicroCMS\Domain\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserDAO extends DAO implements UserProviderInterface
{
    public function find($id)
    {
        $sql = "select * from user where id = ?";
        $row = $this->getDb()->fetchAssoc($sql, [$id]);

        if ($row) {
            return $this->buildDomainObject($row);
        } else {
            throw new \Exception("No user matching id " . $id);
        }
    }

    public function findAll() {
        $sql = "select * from user order by id desc";
        $result = $this->getDb()->fetchAll($sql);

        $entities = array();
        foreach ($result as $row) {
            $id = $row['id'];
            $entities[$id] = $this->buildDomainObject($row);
        }

        return $entities;
    }

    public function save(User $user)
    {
        $userData = [
            'username' => $user->getUsername(),
            'salt' => $user->getSalt(),
            'password' => $user->getPassword(),
            'role' => $user->getRole()
        ];

        if ($user->getId()) {
            $this->getDb()->update('user', $userData, ['id' => $user->getId()]);
        } else {
            $this->getDb()->insert('user', $userData);

            $id = $this->getDb()->lastInsertId();
            $user->setId($id);
        }
    }

    public function delete($id)
    {
        $this->getDb()->delete('user', ['id' => $id]);
    }

    public function loadUserByUsername($username)
    {
        $sql = "select * from user where username = ?";
        $row = $this->getDb()->fetchAssoc($sql, [$username]);

        if ($row) {
            return $this->buildDomainObject($row);
        } else {
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
        }
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);

        if (! $this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'MicroCMS\Domain\User';
    }

    protected function buildDomainObject(array $row)
    {
        $user = new User();

        $user->setId($row['id']);
        $user->setUsername($row['username']);
        $user->setPassword($row['password']);
        $user->setSalt($row['salt']);
        $user->setRole($row['role']);

        return $user;
    }
}