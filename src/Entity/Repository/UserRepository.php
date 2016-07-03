<?php

namespace Carcel\Bundle\UserBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * User repository.
 *
 * @author    Damien Carcel (damien.carcel@gmail.com)
 * @copyright 2016 Damien Carcel (https://github.com/damien-carcel)
 * @license   https://opensource.org/licenses/mit   The MIT license (MIT)
 */
class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
