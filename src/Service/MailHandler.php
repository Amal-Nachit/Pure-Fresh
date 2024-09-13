<?php
namespace App\Service;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
class MailHandler
{
    private MailerInterface $mailer;

    public function __construct(
        MailerInterface $mailer,
    ) {
        $this->mailer = $mailer;
    }

    public function sendTemplateEmail(string $email, string $subject, string $template, array $context)
    {
        $email = (new TemplatedEmail())
            ->from('pure.fresh.website@gmail.com') # paramètrable dans config/packages/mailer.yaml
            ->to('amal_fes@live.fr')
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        $this->mailer->send($email);
    }
}
