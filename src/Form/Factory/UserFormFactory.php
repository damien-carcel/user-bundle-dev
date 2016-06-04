<?php

namespace Carcel\Bundle\UserBundle\Form\Factory;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Creates forms for User entity.
 *
 * @author    Damien Carcel (damien.carcel@gmail.com)
 * @copyright 2016 Damien Carcel (https://github.com/damien-carcel)
 * @license   https://opensource.org/licenses/mit   The MIT license (MIT)
 */
class UserFormFactory implements UserFormFactoryInterface
{
    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var RouterInterface */
    protected $router;

    /** @var TranslatorInterface */
    protected $translator;

    /**
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface      $router
     * @param TranslatorInterface  $translator
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->formFactory = $formFactory;
        $this->router      = $router;
        $this->translator  = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function createCreateForm($item, $type, $url)
    {
        $form = $this->formFactory->create(
            $type,
            $item,
            [
                'action' => $this->router->generate($url),
                'method' => 'POST',
            ]
        );

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function createEditForm($item, $type, $url)
    {
        $form = $this->formFactory->create(
            $type,
            $item,
            [
                'action' => $this->router->generate($url, ['id' => $item->getId()]),
                'method' => 'PUT',
            ]
        );

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function createDeleteForm($id, $url)
    {
        $formBuilder = $this->formFactory->createBuilder();

        $formBuilder->setAction($this->router->generate($url, ['id' => $id]));
        $formBuilder->setMethod('DELETE');
        $formBuilder->add(
            'submit',
            SubmitType::class,
            [
                'label' => $this->translator->trans('carcel_user.button.delete'),
                'attr'  => [
                    'onclick' => sprintf(
                        'return confirm("%s")',
                        $this->translator->trans('carcel_user.notice.delete.confirmation')
                    ),
                    'class'   => 'btn btn-sm btn-default'
                ],
            ]
        );

        return $formBuilder->getForm();
    }

    /**
     * {@inheritdoc}
     */
    public function createDeleteForms(array $items, $url)
    {
        $deleteForms = [];

        foreach ($items as $item) {
            $deleteForms[$item->getId()] = $this->createDeleteForm($item->getId(), $url)->createView();
        }

        return $deleteForms;
    }

    /**
     * {@inheritdoc}
     */
    public function createSetRoleForm(array $choices, $currentRole)
    {
        $form = $this->formFactory
            ->createBuilder()
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => $choices,
                    'label'   => false,
                    'data'    => $currentRole,
                ]
            )
            ->add('submit', SubmitType::class, ['label' => $this->translator->trans('carcel_user.button.change')])
            ->getForm();

        return $form;
    }
}
