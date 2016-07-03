<?php

namespace Carcel\Bundle\UserBundle\Controller;

use Carcel\Bundle\UserBundle\Entity\Repository\UserRepositoryInterface;
use Carcel\Bundle\UserBundle\Form\Factory\UserFormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Global administration of the application users.
 *
 * @author    Damien Carcel (damien.carcel@gmail.com)
 * @copyright 2016 Damien Carcel (https://github.com/damien-carcel)
 * @license   https://opensource.org/licenses/mit   The MIT license (MIT)
 */
class AdminController extends Controller
{
    /**
     * Returns a list of all the application users, except the SUPER_ADMIN.
     *
     * @return Response
     */
    public function indexAction()
    {
        $currentUser = $this->getUser();
        $users       = $this->getUserRepository()->getAllBut($currentUser->getId());
        $deleteForms = $this->getFormCreator()->createDeleteForms($users, 'carcel_user_admin_remove');

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
     * @param int $id
     *
     * @return Response
     */
    public function showProfileAction($id)
    {
        $user = $this->getUserRepository()->findOneByIdOr404($id);

        return $this->render(
            'CarcelUserBundle:Admin:show.html.twig',
            ['user' => $user]
        );
    }

    /**
     * Changes the user's role.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse|Response
     */
    public function setRoleAction(Request $request, $id)
    {
        $rolesManager = $this->get('carcel_user.manager.roles');
        $user         = $this->getUserRepository()->findOneByIdOr404($id);
        $userRole     = $rolesManager->getUserRole($user);
        $choices      = $rolesManager->getChoices();
        $form         = $this->getFormCreator()->createSetRoleForm($choices, $userRole);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $selectedRole = $form->getData();
            $user->setRoles([$choices[$selectedRole['roles']]]);

            $this->get('doctrine.orm.entity_manager')->flush();

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
     * Displays a form to edit a user profile.
     *
     * @param int $id
     *
     * @return RedirectResponse|Response
     */
    public function editProfileAction($id)
    {
        $user = $this->getUserRepository()->findOneByIdOr404($id);
        $form = $this->getFormCreator()->createEditForm(
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
     * @param int     $id
     *
     * @return RedirectResponse|Response
     */
    public function updateProfileAction(Request $request, $id)
    {
        $user = $this->getUserRepository()->findOneByIdOr404($id);
        $form = $this->getFormCreator()->createEditForm(
            $user,
            'Carcel\Bundle\UserBundle\Form\Type\UserType',
            'carcel_user_admin_update'
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('doctrine.orm.entity_manager')->flush();

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
     * Removes a user account and send a message to warn the user that
     * his account has been destroyed.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse|Response
     */
    public function removeUserAction(Request $request, $id)
    {
        $form = $this->getFormCreator()->createDeleteForm($id, 'carcel_user_admin_remove');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user     = $this->getUserRepository()->findOneByIdOr404($id);
            $email    = $user->getEmail();
            $username = $user->getUsername();

            $this->get('doctrine.orm.entity_manager')->remove($user);
            $this->get('doctrine.orm.entity_manager')->flush();

            $this->addFlash(
                'notice',
                $this->get('translator')->trans('carcel_user.notice.delete.label')
            );

            $this->get('carcel_user.manager.mail')->send($email, $username);
        }

        return $this->redirect($this->generateUrl('carcel_user_admin_index'));
    }

    /**
     * @return UserRepositoryInterface
     */
    protected function getUserRepository()
    {
        return $this->get('doctrine.orm.entity_manager')->getRepository('CarcelUserBundle:User');
    }

    /**
     * @return UserFormFactoryInterface
     */
    protected function getFormCreator()
    {
        return $this->get('carcel_user.factory.user_form');
    }
}
