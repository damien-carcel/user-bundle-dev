<?php

namespace Carcel\Bundle\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * User repository.
 *
 * @author    Damien Carcel (damien.carcel@gmail.com)
 * @copyright 2016 Damien Carcel (https://github.com/damien-carcel)
 * @license   https://opensource.org/licenses/mit   The MIT license (MIT)
 */
class UserRepository extends EntityRepository
{
    /**
     * Finds and returns a User entity from its ID.
     *
     * @param int $id
     *
     * @return User
     *
     * @throws NotFoundHttpException
     */
    public function findOneByIdOr404($id)
    {
        $user = $this->find($id);

        if (null === $user) {
            throw new  NotFoundHttpException(
                'The user with the ID %id% does not exists',
                ['%id%' => $id]
            );
        }

        return $user;
    }

    /**
     * Retrieve all users but one.
     *
     * @param int $id The user we don\'t want to be returned.
     *
     * @return array
     */
    public function getAllBut($id)
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.id != :id')
            ->setParameter('id', $id)
            ->orderBy('u.username')
            ->getQuery();

        return $query->getResult();
    }
}
