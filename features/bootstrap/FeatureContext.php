<?php
/*
 * This file is part of CarcelUserBundle.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Context;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Defines application features for authentication.
 *
 * @author Damien Carcel (damien.carcel@gmail.com)
 */
class FeatureContext extends MinkContext implements KernelAwareContext
{
    /** @var KernelInterface */
    protected $kernel;

    /** @var SessionInterface */
    protected $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Checks that user is authenticated.
     *
     * @param string $username
     *
     * @Then /^I should be authenticated as "(?P<username>(?:[^"]|\\")*)"$/
     */
    public function iShouldBeAuthenticatedAs($username)
    {
        $tokenStorage = $this->getTockenStorage();

        if (null === $token = $tokenStorage->getToken()) {
            throw new TokenNotFoundException();
        }

        \PHPUnit_Framework_Assert::assertEquals($token->getUsername(), $username);
    }

    /**
     * Checks that user is not authenticated.
     *
     * @Given /^I am anonymous$/
     * @Then /^I should be anonymous$/
     */
    public function iShouldBeAnonymous()
    {
        $checker = $this->getAuthorizationChecker();

        \PHPUnit_Framework_Assert::assertTrue($checker->isGranted('IS_AUTHENTICATED_ANONYMOUSLY'));
        \PHPUnit_Framework_Assert::assertFalse($checker->isGranted('ROLE_USER'));
    }

    /**
     * Resets an user password.
     *
     * @param string $username
     *
     * @Given /^I reset "(?P<username>(?:[^"]|\\")*)" password$/
     */
    public function iResetUserPassword($username)
    {
        $user = $this->getUserProvider()->loadUserByUsername($username);

        $user->setPasswordRequestedAt(new \DateTime());
        $this->getUserManager()->updateUser($user);
    }

    /**
     * @return TokenStorageInterface
     */
    protected function getTockenStorage()
    {
        return $this->kernel->getContainer()->get('security.token_storage');
    }

    /**
     * @return AuthorizationCheckerInterface
     */
    protected function getAuthorizationChecker()
    {
        return $this->kernel->getContainer()->get('security.authorization_checker');
    }

    /**
     * @return UserProviderInterface
     */
    protected function getUserProvider()
    {
        return $this->kernel->getContainer()->get('fos_user.user_provider.username');
    }

    /**
     * @return UserManagerInterface
     */
    protected function getUserManager()
    {
        return $this->kernel->getContainer()->get('fos_user.user_manager');
    }
}
