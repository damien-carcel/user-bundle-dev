<?php

namespace Carcel\Bundle\UserBundle\Manager;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Mail manager.
 *
 * @author    Damien Carcel (damien.carcel@gmail.com)
 * @copyright 2016 Damien Carcel (https://github.com/damien-carcel)
 * @license   https://opensource.org/licenses/mit   The MIT license (MIT)
 */
class MailManager
{
    /** @var string */
    protected $locale;

    /** @var string */
    protected $mailerAddress;

    /** @var \Swift_Mailer */
    protected $mailer;

    /** @var EngineInterface */
    protected $templating;

    /** @var TranslatorInterface */
    protected $translator;

    /**
     * @param EngineInterface     $templating
     * @param \Swift_Mailer       $mailer
     * @param TranslatorInterface $translator
     * @param string              $locale
     * @param string              $mailerAddress
     */
    public function __construct(
        EngineInterface $templating,
        \Swift_Mailer $mailer,
        TranslatorInterface $translator,
        $locale,
        $mailerAddress
    ) {
        $this->templating    = $templating;
        $this->mailer        = $mailer;
        $this->translator    = $translator;
        $this->locale        = $locale;
        $this->mailerAddress = $mailerAddress;
    }

    /**
     * Sends an email.
     *
     * @param string $email
     * @param string $username
     */
    public function send($email, $username)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($this->translator->trans('carcel_user.account.remove'))
            ->setFrom($this->mailerAddress)
            ->setTo($email)
            ->setBody(
                $this->templating->render(
                    'CarcelUserBundle:Admin:email.txt.twig',
                    ['username' => $username]
                )
            );

        $this->mailer->send($message);
    }
}
