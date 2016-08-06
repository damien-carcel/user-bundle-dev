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
use Carcel\Bundle\UserBundle\Entity\Repository\UserRepositoryInterface;
use FOS\UserBundle\Model\UserInterface;
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
     * Checks what users are listed in the admin page.
     *
     * @param string $list
     *
     * @Then /^I should see the users? "([^"]*)"$/
     */
    public function iShouldSeeTheFollowingUsers($list)
    {
        $providedUserNames = $this->listToArray($list);
        sort($providedUserNames);

        $storedUsers = $this->getUserRepository()->findAllBut(
            $this->getTockenStorage()->getToken()->getUser()
        );

        $userNames = [];
        foreach ($storedUsers as $storedUser) {
            $userNames[] = $storedUser->getUsername();
        }
        sort($userNames);

        \PHPUnit_Framework_Assert::assertEquals($providedUserNames, $userNames);
    }

    /**
     * Launches an action for a specific username from a hyperlink.
     *
     * @param string $action
     * @param string $username
     *
     * @When /^I follow "(?P<action>(?:[^"]|\\")*)" for "(?P<user>(?:[^"]|\\")*)" profile$/
     */
    public function iFollowTheActionLinkForTheUserProfile($action, $username)
    {
        $action = $this->fixStepArgument($action);

        $row = $this->findUserRowByText($username);
        $link = $row->findLink($action);

        \PHPUnit_Framework_Assert::assertNotNull($link, 'Cannot find link in row with text '.$action);
        $link->click();
    }

    /**
     * Launches an action for a specific username from an input buttons.
     *
     * @param string $action
     * @param string $username
     *
     * @When /^I press "(?P<action>(?:[^"]|\\")*)" for "(?P<user>(?:[^"]|\\")*)" profile$/
     */
    public function iPressTheActionLinkForTheUserProfile($action, $username)
    {
        $action = $this->fixStepArgument($action);

        $row = $this->findUserRowByText($username);
        $button = $row->findButton($action);

        \PHPUnit_Framework_Assert::assertNotNull($button, 'Cannot find button in row with text '.$action);
        $button->press();
    }

    /**
     * Asserts that a user have a defined role.
     *
     * @param string $username
     * @param string $role
     *
     * @Then /^user "(?P<username>(?:[^"]|\\")*)" should have role "(?P<role>(?:[^"]|\\")*)"$/
     */
    public function userShouldHaveRole($username, $role)
    {
        $user = $this->getUserRepository()->findOneBy(['username' => $username]);
        \PHPUnit_Framework_Assert::assertTrue($user->hasRole($role));
    }

    /**
     * Finds a table row according to its content.
     *
     * @param $username
     *
     * @return mixed
     */
    protected function findUserRowByText($username)
    {
        $row = $this->getSession()->getPage()->find('css', sprintf('table tr:contains("%s")', $username));

        \PHPUnit_Framework_Assert::assertNotNull($row, 'Cannot find a table row with username '.$username);

        return $row;
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

    /**
     * @param string $list
     *
     * @return string[]
     */
    protected function listToArray($list)
    {
        if (empty($list)) {
            return [];
        }

        return explode(', ', str_replace(' and ', ', ', $list));
    }

    /**
     * @return UserRepositoryInterface
     */
    protected function getUserRepository()
    {
        return $this->kernel->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('CarcelUserBundle:User');
    }
}
