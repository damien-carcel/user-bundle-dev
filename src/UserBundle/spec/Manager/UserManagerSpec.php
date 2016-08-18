<?php

/*
 * This file is part of CarcelUserBundle.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Carcel\Bundle\UserBundle\Manager;

use Carcel\Bundle\UserBundle\Entity\Repository\UserRepositoryInterface;
use Carcel\Bundle\UserBundle\Entity\User;
use Carcel\Bundle\UserBundle\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @author Damien Carcel (damien.carcel@gmail.com)
 */
class UserManagerSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->beConstructedWith($tokenStorage, $entityManager, User::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UserManager::class);
    }

    function it_returns_all_users_but_the_current_one_and_the_super_admin(
        $tokenStorage,
        $entityManager,
        UserRepositoryInterface $userRepository,
        TokenInterface $token,
        UserInterface $currentUser,
        UserInterface $superAdmin,
        UserInterface $regularUser
    ) {
        $entityManager->getRepository(User::class)->willReturn($userRepository);

        $currentUser->setRoles(['ROLE_ADMIN']);
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $regularUser->setRoles(['ROLE_USER']);

        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($currentUser);

        $currentUser->hasRole('ROLE_SUPER_ADMIN')->willReturn(false);
        $userRepository->findByRole('ROLE_SUPER_ADMIN')->willReturn([$superAdmin]);
        $userRepository->findAllBut([$currentUser, $superAdmin])->willReturn([$regularUser]);

        $this->getAdministrableUsers()->shouldReturn([$regularUser]);
    }

    function it_returns_all_users_but_the_current_one_being_the_super_admin(
        $tokenStorage,
        $entityManager,
        UserRepositoryInterface $userRepository,
        TokenInterface $token,
        UserInterface $currentUser,
        UserInterface $regularAdmin,
        UserInterface $regularUser
    ) {
        $entityManager->getRepository(User::class)->willReturn($userRepository);

        $currentUser->setRoles(['ROLE_SUPER_ADMIN']);
        $regularAdmin->setRoles(['ROLE_ADMIN']);
        $regularUser->setRoles(['ROLE_USER']);

        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($currentUser);

        $currentUser->hasRole('ROLE_SUPER_ADMIN')->willReturn(true);
        $userRepository->findByRole()->shouldNotBeCalled();
        $userRepository->findAllBut([$currentUser])->willReturn([$regularAdmin, $regularUser]);

        $this->getAdministrableUsers()->shouldReturn([$regularAdmin, $regularUser]);
    }
}
