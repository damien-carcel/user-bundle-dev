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

use Carcel\Bundle\UserBundle\Manager\RolesManager;
use FOS\UserBundle\Model\UserInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Damien Carcel (damien.carcel@gmail.com)
 */
class RolesManagerSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage, TranslatorInterface $translator)
    {
        $this->beConstructedWith(
            $tokenStorage,
            $translator,
            [
                'ROLE_EDITOR'      => ['ROLE_USER'],
                'ROLE_ADMIN'       => ['ROLE_EDITOR'],
                'ROLE_SUPER_ADMIN' => ['ROLE_ADMIN'],
            ]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RolesManager::class);
    }

    function it_returns_the_list_of_roles_for_the_super_admin(
        $tokenStorage,
        TokenInterface $token,
        UserInterface $currentUser
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($currentUser);
        $currentUser->isSuperAdmin()->willReturn(true);

        $this->getChoices()->shouldReturn([
            'ROLE_USER'   => 'ROLE_USER',
            'ROLE_EDITOR' => 'ROLE_EDITOR',
            'ROLE_ADMIN'  => 'ROLE_ADMIN',
        ]);
    }

    function it_returns_the_list_of_roles_for_regular_admin(
        $tokenStorage,
        TokenInterface $token,
        UserInterface $currentUser
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($currentUser);
        $currentUser->isSuperAdmin()->willReturn(false);

        $this->getChoices()->shouldReturn([
            'ROLE_USER'   => 'ROLE_USER',
            'ROLE_EDITOR' => 'ROLE_EDITOR',
        ]);
    }

    function it_returns_the_list_of_roles_for_anonymous_user($tokenStorage, TokenInterface $token)
    {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn('anonymous');

        $this->getChoices()->shouldReturn([
            'ROLE_USER'   => 'ROLE_USER',
            'ROLE_EDITOR' => 'ROLE_EDITOR',
        ]);
    }

    function it_returns_the_list_of_roles_from_command_line($tokenStorage)
    {
        $tokenStorage->getToken()->willReturn(null);

        $this->getChoices()->shouldReturn([
            'ROLE_USER'   => 'ROLE_USER',
            'ROLE_EDITOR' => 'ROLE_EDITOR',
            'ROLE_ADMIN'  => 'ROLE_ADMIN',
        ]);
    }

    function it_returns_the_role_of_a_user(UserInterface $user)
    {
        $user->getRoles()->willReturn(['ROLE_USER']);

        $this->getUserRole($user)->shouldReturn('ROLE_USER');
    }
}
