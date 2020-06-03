<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        // UserPasswordEncoderInterface : classe qui permet d'encoder / hacher de mots de passes
        $user = new User;

        $form = $this->createForm(RegistrationType::class, $user); // Pourquoi le deuxieme argument est $user ? car Le RegistrationType
        // Va nous permettre de remplire $user
        // Va me chercher RegistrationType qui va me remplir $user

        dump($request);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            // On récupère le mot de passe du formulaire (non haché pour le moment) pour le transmettre à la méthode 
            // encodePassword() qui va se chargé d'encoder / crypter / hacher le mot de passe 
            // Pour pouvoir encoder le mot de passe il faut que notre entité User possède certaine classe (5 classes)

            $user->setPassword($hash); // on envoi le mot de passe haché dans le setteur de l'objet $user afin qu'il soit inséré
            // dans la BDD

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security_login'); // On redirige vers ma page de connexion après inscription
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /** 
     * @Route("/connexion", name="security_login")
     */
    public function login()
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
        // cette fonctionj ne retourne rien, il nous suffit d'avoir une route pour la deconnexion (voir security.yaml / firewalls)
    }

    /*
        security.yaml :

        providers : où se trouvent les données à contrôler
        fireWalls : quelles parties du site nous allons protéger et par quel moyen (formulaire de connexion)
    */
}
