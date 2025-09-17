<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        if ($request->isMethod('POST')) {
            $user = new User();

            // On récupère les champs du formulaire
            $user->setPrenom($request->request->get('prenom'));
            $user->setNom($request->request->get('nom'));
            $user->setEmail($request->request->get('email'));

            // Hash du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $request->request->get('password'));
            $user->setPassword($hashedPassword);

            // Image de profil par défaut si aucune image uploadée
            if ($request->files->get('profile_image')) {
                $file = $request->files->get('profile_image');
                $filename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('profiles_directory'), $filename);
                $user->setProfileImage($filename);
            } else {
                $user->setProfileImage('default.jpg');
            }

            // ✅ plus besoin de setCreatedAt -> géré automatiquement par le Trait
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig');
    }
}
