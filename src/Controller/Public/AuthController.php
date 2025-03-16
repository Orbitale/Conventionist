<?php

namespace App\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class AuthController extends AbstractController
{
    public const string LOGIN_ROUTE_NAME = 'login';

    public const array LOGIN_PATHS = [
        'fr' => '/connexion',
        'en' => '/login',
    ];

    public const array LOGOUT_PATHS = [
        'fr' => '/deconnexion',
        'en' => '/logout',
    ];

    #[Route(path: self::LOGIN_PATHS, name: self::LOGIN_ROUTE_NAME, methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }
}
