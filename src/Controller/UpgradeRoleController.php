<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Routing\Attribute\Route;

final class UpgradeRoleController extends AbstractController
{
   #[Route('/upgrade-role', name: 'app_upgrade_role', methods: ['GET', 'POST'])]
public function upgradeRole(Request $request, EntityManagerInterface $entityManager): Response
{
    // Get the currently logged-in user
    $user = $this->getUser();

    // Check if the user is logged in
    if (!$user) {
        // If no user is logged in, redirect to login page or show an error
        return $this->redirectToRoute('app_login');
    }

    if ($request->isMethod('POST')) {
        $password = $request->request->get('upgrade_password');

        // Check if the provided password is correct
        if ($password === 'admin123') {
            // Set the user's roles
            $user->setRoles(['ADMIN']);

            // Persist changes to the database
            $entityManager->flush();

            // Redirect or show a success message
            return $this->redirectToRoute('app_admin'); // Replace with your success route
        } else {
            // Optionally, handle incorrect password (e.g., show error message)
            $this->addFlash('error', 'Incorrect password.');
        }
    }

    return $this->render('upgrade_role/index.html.twig', [
        'user' => $user,  // Pass user data to the template if needed
    ]);
}

}
