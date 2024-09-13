<?php
namespace App\Controller;
use App\Service\MailHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class MailController extends AbstractController
{
    private MailHandler $mailHandler;
    public function __construct(MailHandler $mailHandler)
    {
        $this->mailHandler = $mailHandler;
    }
    #[Route('/email', name: 'app_email')]
    public function index(): Response
    {
        $this->mailHandler->sendTemplateEmail(
            "pure.fresh.website@gmail.com",
            "Email de test from Symfony",
            "mail/mailTemplate.html.twig",
            []
        );
        return $this->render('mail/index.html.twig', [
            'controller_name' => 'MailController',
        ]);
    }
}