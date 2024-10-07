<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AccessDeniedListener implements EventSubscriberInterface
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Priorité pour que cet écouteur soit appelé avant les autres
            KernelEvents::EXCEPTION => ['onKernelException', 2],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Vérifie si l'exception est bien une AccessDeniedException
        if (!$exception instanceof AccessDeniedException) {
            return;
        }

        // Récupère un message d'erreur personnalisé ou celui de l'exception
        $message = 'Accès refusé. Vous n\'avez pas les permissions nécessaires.';
        
        // Ajouter le message à la session
        $event->getRequest()->getSession()->getFlashBag()->add('error', $message);

        // Rediriger l'utilisateur vers la page d'accueil (ou une autre page)
        $response = new RedirectResponse($this->urlGenerator->generate('home'));

        // Définit la réponse et arrête la propagation de l'événement
        $event->setResponse($response);
        $event->stopPropagation();
    }
}
