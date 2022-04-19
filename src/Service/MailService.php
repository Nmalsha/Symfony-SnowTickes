<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailService implements MailServiceInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var string
     */
    private $defaultMail;

    public function __construct(string $defaultMail, MailerInterface $mailer)
    {
        $this->defaultMail = $defaultMail;
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(string $to, string $subject, string $htmlTemplate, array $context): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address('malshis@yahoo.com', 'SnowTrick'))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($htmlTemplate)
            ->context($context);

        $this->mailer->send($email);
    }
}
