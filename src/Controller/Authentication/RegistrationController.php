<?php

namespace App\Controller\Authentication;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{


    #[Route('', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les rôles depuis le formulaire
            $roles = $form->get('roles')->getData();
            $user->setRoles($roles);

            // Enregistrer le mot de passe en clair (uniquement pour les tests)
            $user->setPassword($form->get('password')->getData());

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                "success",
                "Votre compte a bien été créé !"
            );

            if (in_array('ROLE_ADMIN', $roles)) {
                return $this->redirectToRoute('app_blog_index');
            } else {
                return $this->redirectToRoute('app_user_blog');
            }
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}




