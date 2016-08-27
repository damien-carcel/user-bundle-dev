<?php

/*
 * This file is part of CarcelUserBundle.
 *
 * Copyright (c) 2016 Damien Carcel <damien.carcel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Carcel\Bundle\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;

/**
 * User entity.
 *
 * @author Damien Carcel (damien.carcel@gmail.com)
 */
class User extends BaseUser
{
    /** @var int */
    protected $id;
}
