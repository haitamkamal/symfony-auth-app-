<?php
// src/Controller/LoginController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Check if the user is already authenticated
        if ($this->getUser()) {
            // Store user information in the session
            $session = $request->getSession();
            $session->set('user', [
                'id' => $this->getUser()->getId(),
                'username' => $this->getUser()->getUsername(),
                'email' => $this->getUser()->getEmail(),
            ]);

            // Create a cookie to remember the user (expires in 20 seconds)
            $response = new Response();
            $cookie = new Cookie(
            'remember_me', // Cookie name
            $this->getUser()->getUsername(), // Cookie value
            time() + 3600 * 24 * 7 // Expiration time (1 week)
                );
            $response->headers->setCookie($cookie);

            // Set session lifetime and invalidate after 20 seconds
            $session->migrate(true); // Regenerate the session ID to prevent session fixation attack

            // Redirect to a specific route after login
            return $this->redirectToRoute('dashboard', [], Response::HTTP_SEE_OTHER, $response);
        }

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(Request $request): Response
    {
        // Clear the session
        $session = $request->getSession();
        $session->clear();

        // Clear the remember_me cookie
        $response = new Response();
        $response->headers->clearCookie('remember_me');

        // Redirect to the homepage or login page
        return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER, $response);
    }
}
