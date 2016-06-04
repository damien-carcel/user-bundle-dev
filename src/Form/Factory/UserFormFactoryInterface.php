<?php

namespace Carcel\Bundle\UserBundle\Form\Factory;

/**
 * Create forms for User entity.
 *
 * @author    Damien Carcel (damien.carcel@gmail.com)
 * @copyright 2016 Damien Carcel (https://github.com/damien-carcel)
 * @license   https://opensource.org/licenses/mit   The MIT license (MIT)
 */
interface UserFormFactoryInterface
{
    /**
     * Creates a form to create a new entity.
     *
     * @param object $item The entity to create.
     * @param string $type The form type to use with the entity
     * @param string $url  The route used to create the entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createCreateForm($item, $type, $url);

    /**
     * Creates a form to edit an entity.
     *
     * @param object $item The entity to edit.
     * @param string $type The form type to use with the entity
     * @param string $url  The route used to edit the entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createEditForm($item, $type, $url);

    /**
     * Creates a form to delete an entity.
     *
     * @param int    $id  The ID of the entity to delete
     * @param string $url The route used to delete the entity
     *
     * @return \Symfony\Component\Form\Form
     */
    public function createDeleteForm($id, $url);

    /**
     * Return a list of delete forms for a set entities.
     *
     * @param object[] $items The list of entities to delete
     * @param string   $url   The route used to delete the entities
     *
     * @return \Symfony\Component\Form\Form[]
     */
    public function createDeleteForms(array $items, $url);
    
    /**
     * Creates a form to set user's roles.
     *
     * @param array  $choices
     * @param string $currentRole
     *
     * @return \Symfony\Component\Form\Form
     */
    public function createSetRoleForm(array $choices, $currentRole);
}
