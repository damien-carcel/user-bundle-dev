<?php

namespace Carcel\Bundle\UserBundle\Entity\Repository;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * User repository interface.
 *
 * @author    Damien Carcel (damien.carcel@gmail.com)
 * @copyright 2016 Damien Carcel (https://github.com/damien-carcel)
 * @license   https://opensource.org/licenses/mit   The MIT license (MIT)
 */
interface UserRepositoryInterface
{
    /**
     * Finds and returns a User entity from its ID.
     *
     * @param int $id
     *
     * @throws NotFoundHttpException
     *
     * @return UserInterface
     */
    public function findOneByIdOr404($id);

    /**
     * Retrieve all users but one.
     *
     * @param int $id The user we don\'t want to be returned.
     *
     * @return array
     */
    public function getAllBut($id);
}
