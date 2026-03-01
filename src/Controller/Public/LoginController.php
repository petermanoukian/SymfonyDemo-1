<?php
// src/Controller/Public/LoginController.php

namespace App\Controller\Public; // Added \Public here

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController // Renamed class
{
// src/Controller/SecurityController.php

    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // If user is already logged in, send them away from the login page
        if ($this->getUser()) {
            $roles = $this->getUser()->getRoles();

            if (in_array('ROLE_SUPER_ADMIN', $roles)) {
                return $this->redirectToRoute('app_superadmin_cat_index');
            }

            if (in_array('ROLE_ADMIN', $roles)) {
                return $this->redirectToRoute('app_admin_dashboard'); // Or your admin route
            }

            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('public/auth/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    public function logout(): void
    {
        // This is still intercepted by the firewall
    }
}