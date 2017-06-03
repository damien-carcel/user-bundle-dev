<?php

/*
 * This file is part of CarcelUserBundle.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carcel\Bundle\UserBundle\Controller;

use Carcel\Bundle\UserBundle\Event\UserEvents;
use Carcel\Bundle\UserBundle\Form\Factory\UserFormFactoryInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Global administration of the application users.
 *
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class AdminController extends Controller
{
    /**
     * Renders the list of administrated users.
     *
     * @return Response
     */
    public function indexAction()
    {
        $users = $this->get('carcel_user.manager.users')->getAdministrableUsers();
        $deleteForms = $this->getUserFormFactory()->createDeleteFormViews($users, 'carcel_user_admin_remove');

        return $this->render(
            'CarcelUserBundle:Admin:index.html.twig',
            [
                'users'        => $users,
                'delete_forms' => $deleteForms,
            ]
        );
    }

    /**
     * Shows a user profile.
     *
     * @param string $username
     *
     * @return Response
     */
    public function showProfileAction($username)
    {
        $user = $this->findUserByUsernameOr404($username);

        return $this->render(
            'CarcelUserBundle:Admin:show.html.twig',
            ['user' => $user]
        );
    }

    /**
     * Displays a form to edit a user profile.
     *
     * @param string $username
     *
     * @return RedirectResponse|Response
     */
    public function editProfileAction($username)
    {
        $user = $this->findUserByUsernameOr404($username);
        $form = $this->getUserFormFactory()->createEditForm(
            $user,
            'Carcel\Bundle\UserBundle\Form\Type\UserType',
            'carcel_user_admin_update'
        );

        return $this->render(
            'CarcelUserBundle:Admin:edit.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Updates a user's profile.
     *
     * @param Request $request
     * @param string  $username
     *
     * @return RedirectResponse|Response
     */
    public function updateProfileAction(Request $request, $username)
    {
        $user = $this->findUserByUsernameOr404($username);
        $form = $this->getUserFormFactory()->createEditForm(
            $user,
            'Carcel\Bundle\UserBundle\Form\Type\UserType',
            'carcel_user_admin_update'
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('event_dispatcher')->dispatch(UserEvents::PRE_UPDATE, new GenericEvent($user));

            $this->get('doctrine.orm.entity_manager')->flush();

            $this->get('event_dispatcher')->dispatch(UserEvents::POST_UPDATE, new GenericEvent($user));

            $this->addFlash(
                'notice',
                $this->get('translator')->trans('carcel_user.notice.update')
            );

            return $this->redirect($this->generateUrl('carcel_user_admin_index'));
        }

        return $this->render(
            'CarcelUserBundle:Admin:edit.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Changes the user's role.
     *
     * @param Request $request
     * @param string  $username
     *
     * @throws AccessDeniedException
     *
     * @return RedirectResponse|Response
     */
    public function setRoleAction(Request $request, $username)
    {
        $user = $this->findUserByUsernameOr404($username);

        if (!$this->getUser()->isSuperAdmin() && $user->hasRole('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $rolesManager = $this->get('carcel_user.manager.roles');
        $userManager = $this->get('carcel_user.manager.users');

        $userRole = $rolesManager->getUserRole($user);

        $form = $this->getUserFormFactory()->createSetRoleForm($userRole);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $selectedRole = $form->getData();

            $this->get('event_dispatcher')->dispatch(UserEvents::PRE_SET_ROLE, new GenericEvent($user));

            $userManager->setRole($user, $selectedRole);

            $this->get('event_dispatcher')->dispatch(UserEvents::POST_SET_ROLE, new GenericEvent($user));

            $this->addFlash(
                'notice',
                $this->get('translator')->trans('carcel_user.notice.set_role')
            );

            return $this->redirect($this->generateUrl('carcel_user_admin_index'));
        }

        return $this->render(
            'CarcelUserBundle:Admin:set_role.html.twig',
            [
                'form'     => $form->createView(),
                'username' => $user->getUsername(),
            ]
        );
    }

    /**
     * Activates or deactivates a user.
     *
     * @param $username
     *
     * @throws AccessDeniedException
     *
     * @return RedirectResponse
     */
    public function changeStatusAction($username)
    {
        $user = $this->findUserByUsernameOr404($username);

        if (!$this->getUser()->isSuperAdmin() && $user->hasRole('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if ($user->isEnabled()) {
            $this->get('carcel_user.handler.user_status')->disable($user);
            $notice = 'carcel_user.notice.deactivated';
        } else {
            $this->get('carcel_user.handler.user_status')->enable($user);
            $notice = 'carcel_user.notice.activated';
        }

        $this->addFlash(
            'notice',
            $this->get('translator')->trans($notice)
        );

        return $this->redirect($this->generateUrl('carcel_user_admin_index'));
    }

    /**
     * Removes a user account and send a message to warn the user that
     * his account has been destroyed.
     *
     * @param Request $request
     * @param string  $username
     *
     * @return RedirectResponse|Response
     */
    public function removeUserAction(Request $request, $username)
    {
        $user = $this->findUserByUsernameOr404($username);
        $form = $this->getUserFormFactory()->createDeleteForm($username, 'carcel_user_admin_remove');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('event_dispatcher')->dispatch(UserEvents::PRE_REMOVE, new GenericEvent($user));

            $this->get('doctrine.orm.entity_manager')->remove($user);
            $this->get('doctrine.orm.entity_manager')->flush();

            $this->get('event_dispatcher')->dispatch(UserEvents::POST_REMOVE);

            $this->addFlash(
                'notice',
                $this->get('translator')->trans('carcel_user.notice.delete.label')
            );
        }

        return $this->redirect($this->generateUrl('carcel_user_admin_index'));
    }

    /**
     * Finds and returns a User from its username.
     * A regular administrator cannot get the super administrator, as he has no
     * right to access its profile.
     *
     * @param string $username
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     *
     * @return UserInterface
     */
    protected function findUserByUsernameOr404($username)
    {
        $user = $this
            ->get('doctrine.orm.entity_manager')
            ->getRepository('CarcelUserBundle:User')
            ->findOneBy(['username' => $username]);

        if (!$this->getUser()->isSuperAdmin() && $user->isSuperAdmin()) {
            throw $this->createAccessDeniedException();
        }

        if (null === $user) {
            throw new  NotFoundHttpException(sprintf(
                'The user with the name %username% does not exists',
                $username
            ));
        }

        return $user;
    }

    /**
     * @return UserFormFactoryInterface
     */
    protected function getUserFormFactory()
    {
        return $this->get('carcel_user.factory.user_form');
    }
}
