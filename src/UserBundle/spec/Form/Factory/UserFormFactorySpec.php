<?php

/*
 * This file is part of CarcelUserBundle.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Carcel\Bundle\UserBundle\Form\Factory;

use Carcel\Bundle\UserBundle\Form\Factory\UserFormFactory;
use Carcel\Bundle\UserBundle\Form\Factory\UserFormFactoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Damien Carcel <damien.carcel@gmail.com>
 */
class UserFormFactorySpec extends ObjectBehavior
{
    function let(
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->beConstructedWith($formFactory, $router, $translator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UserFormFactory::class);
    }

    function it_is_a_user_form_factory()
    {
        $this->shouldImplement(UserFormFactoryInterface::class);
    }

    function it_creates_a_create_form(
        $formFactory,
        $router,
        UserInterface $user,
        FormInterface $form
    ) {
        $router->generate(Argument::any())->willReturn('user/create');
        $formFactory
            ->create(Argument::any(), $user, [
                'action' => 'user/create',
                'method' => 'POST',
            ])
            ->willReturn($form);

        $this->createCreateForm($user, Argument::any(), Argument::any())->shouldReturn($form);
    }

    function it_creates_an_edit_form(
        $formFactory,
        $router,
        UserInterface $user,
        FormInterface $form
    ) {
        $user->getUsername()->willReturn('name');
        $router->generate(Argument::any(), ['username' => 'name'])->willReturn('user/edit');
        $formFactory
            ->create(Argument::any(), $user, [
                'action' => 'user/edit',
                'method' => 'PUT',
            ])
            ->willReturn($form);

        $this->createEditForm($user, Argument::any(), Argument::any())->shouldReturn($form);
    }

    function it_creates_a_delete_form(
        $formFactory,
        $router,
        $translator,
        FormBuilderInterface $builder,
        FormInterface $form
    ) {
        $formFactory->createBuilder()->willReturn($builder);
        $router->generate(Argument::any(), ['username' => 'name'])->willReturn('user/delete');

        $translator->trans('carcel_user.button.delete')->willReturn('Delete');
        $translator->trans('carcel_user.notice.delete.confirmation')->willReturn('Are you sure?');

        $builder->setAction('user/delete')->willReturn($builder);
        $builder->setMethod('DELETE')->willReturn($builder);
        $builder
            ->add('submit', SubmitType::class, [
                'label' => 'Delete',
                'attr'  => [
                    'class'   => 'btn btn-sm btn-default',
                    'onclick' => 'return confirm("Are you sure?")',
                ],
            ])
            ->willReturn($builder);

        $builder->getForm()->willReturn($form);

        $this->createDeleteForm('name', Argument::any())->shouldReturn($form);
    }

    function it_creates_a_set_of_delete_form_views(
        $formFactory,
        $router,
        $translator,
        FormBuilderInterface $builder,
        UserInterface $user,
        FormInterface $form,
        FormView $formView
    ) {
        $user->getUsername()->willReturn('name');
        $formFactory->createBuilder()->willReturn($builder);
        $router->generate(Argument::any(), ['username' => 'name'])->willReturn('user/delete');

        $translator->trans('carcel_user.button.delete')->willReturn('Delete');
        $translator->trans('carcel_user.notice.delete.confirmation')->willReturn('Are you sure?');

        $builder->setAction('user/delete')->willReturn($builder);
        $builder->setMethod('DELETE')->willReturn($builder);
        $builder
            ->add('submit', SubmitType::class, [
                'label' => 'Delete',
                'attr'  => [
                    'class'   => 'btn btn-sm btn-default',
                    'onclick' => 'return confirm("Are you sure?")',
                ],
            ])
            ->willReturn($builder);

        $builder->getForm()->willReturn($form);
        $form->createView()->willReturn($formView);

        $this->createDeleteFormViews([$user], Argument::any())->shouldReturn(['name' => $formView]);
    }

    function it_creates_a_form_to_set_users_role(
        $formFactory,
        $translator,
        FormBuilderInterface $builder,
        FormInterface $form
    ) {
        $formFactory->createBuilder()->willReturn($builder);
        $translator->trans('carcel_user.button.change')->willReturn('Change');
        $translator->trans('carcel_user.form.role.label')->willReturn('Roles');

        $builder
            ->add('roles', ChoiceType::class, [
                'choices' => ['role1', 'role2', 'role3'],
                'label'   => 'Roles',
                'data'    => 'role1',
            ])
            ->willReturn($builder);

        $builder
            ->add('submit', SubmitType::class, ['label' => 'Change'])
            ->willReturn($builder);

        $builder->getForm()->willReturn($form);

        $this->createSetRoleForm(['role1', 'role2', 'role3'], 'role1');
    }
}
