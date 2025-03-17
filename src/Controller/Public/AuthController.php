<?php

namespace App\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class AuthController extends AbstractController
{
    use TargetPathTrait;

    public const string LOGIN_ROUTE_NAME = 'login';

    public const array LOGIN_PATHS = [
        'fr' => '/connexion',
        'en' => '/login',
    ];

    public const array LOGOUT_PATHS = [
        'fr' => '/deconnexion',
        'en' => '/logout',
    ];

    public function __construct(private readonly AuthenticationUtils $authenticationUtils) {}

    #[Route(path: self::LOGIN_PATHS, name: self::LOGIN_ROUTE_NAME, methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        if ($request->query->has('target')) {
            $this->saveTargetPath($request->getSession(), 'main', $request->query->get('target'));
        }

        return $this->render('login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }
}
