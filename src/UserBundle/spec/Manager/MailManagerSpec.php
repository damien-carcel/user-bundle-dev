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

use Carcel\Bundle\UserBundle\Factory\SwiftMessageFactory;
use Carcel\Bundle\UserBundle\Manager\MailManager;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Damien Carcel (damien.carcel@gmail.com)
 */
class MailManagerSpec extends ObjectBehavior
{
    function let(
        \Swift_Mailer $mailer,
        TranslatorInterface $translator,
        SwiftMessageFactory $messageFactory
    ) {
        $this->beConstructedWith($mailer, $translator, $messageFactory, 'noresponse@example.info');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MailManager::class);
    }

    function it_sends_a_mail_to_a_user(
        $mailer,
        $translator,
        $messageFactory,
        \Swift_Message $message
    ) {
        $messageFactory->create()->willReturn($message);
        $translator->trans('carcel_user.remove.subject')->willReturn('Message subject');
        $translator
            ->trans('carcel_user.remove.content', ['username' => 'user name'])
            ->willReturn('Message body for user name');

        $message->setSubject('Message subject')->willReturn($message);
        $message->setFrom('noresponse@example.info')->willReturn($message);
        $message->setTo('user@exemple.info')->willReturn($message);
        $message->setBody('Message body for user name')->willReturn($message);

        $mailer->send($message)->shouldBeCalled();

        $this->send('user@exemple.info', 'user name', 'carcel_user.remove.subject', 'carcel_user.remove.content');
    }
}
