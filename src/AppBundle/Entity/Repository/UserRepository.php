<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 * @package AppBundle\Entity
 */
class UserRepository extends EntityRepository
{
    private const PARTIAL_FIELDS = 'id,username,roles';

    /**
     * Search partial user with id
     *
     * @param $id
     * @return User|null
     * @throws
     */
    public function findPartialOneById($id)
    {
        return $this->createQueryBuilder('u')
            ->select("partial u.{".self::PARTIAL_FIELDS."}")
            ->where("u.id = :id")
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    /**
     * Search partial user with credentials
     *
     * @param $username
     * @param $password
     * @return User|null
     * @throws
     */
    public function findPartialOneByCredentials($username, $password)
    {
        return $this->createQueryBuilder('u')
            ->select("partial u.{".self::PARTIAL_FIELDS."}")
            ->where("u.username = :username")
            ->andWhere('u.password = :password')
            ->setParameters([
                'username' => $username,
                'password' => $password
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
